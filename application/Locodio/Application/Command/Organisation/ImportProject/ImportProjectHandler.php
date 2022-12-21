<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\Command\Organisation\ImportProject;

use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\AttributeType;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Locodio\Domain\Model\Model\ModelSettingsRepository;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\AssociationRepository;
use App\Locodio\Domain\Model\Model\AssociationType;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use Symfony\Component\Uid\Uuid;

class ImportProjectHandler
{
    public function __construct(
        protected ProjectRepository       $projectRepo,
        protected DomainModelRepository   $domainModelRepo,
        protected AttributeRepository     $attributeRepo,
        protected AssociationRepository   $associationRepo,
        protected EnumRepository          $enumRepo,
        protected EnumOptionRepository    $enumOptionRepo,
        protected QueryRepository         $queryRepo,
        protected CommandRepository       $commandRepo,
        protected TemplateRepository      $templateRepo,
        protected ModelSettingsRepository $modelSettingsRepo,
        protected ModuleRepository        $moduleRepo,
        protected ModelStatusRepository   $modelStatusRepo,
    ) {
    }

    public function go(ImportProject $command): bool
    {
        $projectClass = $command->getImportProject();
        $project = $this->projectRepo->getByUuid(Uuid::fromString($command->getProjectUuid()));

        // -- update the project --------------------------------------------------
        $project->setLayers(
            $projectClass->domainLayer,
            $projectClass->applicationLayer,
            $projectClass->infrastructureLayer
        );

        // -- create the model-settings --------------------------------------------
        $modelSettings = ModelSettings::make(
            $project,
            $this->modelSettingsRepo->nextIdentity(),
            $projectClass->modelSettings->domainLayer,
            $projectClass->modelSettings->applicationLayer,
            $projectClass->modelSettings->infrastructureLayer,
        );
        $this->modelSettingsRepo->save($modelSettings);

        $project->setModelSettings($modelSettings);
        $this->projectRepo->save($project);

        // -- make the modules ----------------------------------------------------
        $moduleEList = [];
        foreach ($projectClass->modules as $module) {
            $moduleE = Module::make(
                $project,
                $this->moduleRepo->nextIdentity(),
                $module->name,
                $module->namespace
            );
            $moduleE->setSequence($module->sequence);
            $this->moduleRepo->save($moduleE);
            $moduleEList[] = $moduleE;
        }

        // -- make the domain models ----------------------------------------------
        $targetModelList = [];
        $domainModelEList = [];

        foreach ($projectClass->domainModels as $domainModel) {
            $domainModelE = DomainModel::make($project, $this->domainModelRepo->nextIdentity(), $domainModel->name);
            $domainModelE->change($domainModel->name, $domainModel->namespace, $domainModel->repository);
            $domainModelE->setSequence($domainModel->sequence);

            // -- link the module ------------------------------------------------
            $moduleE = $this->findModuleByNameAndSequence(
                $domainModel->module->name,
                $domainModel->module->sequence,
                $moduleEList
            );
            $domainModelE->setModule($moduleE);
            $this->domainModelRepo->save($domainModelE);

            // -- import the attributes ------------------------------------------
            foreach ($domainModel->attributes as $attribute) {
                $attributeE = Attribute::make(
                    $domainModelE,
                    $this->attributeRepo->nextIdentity(),
                    $attribute->name,
                    $attribute->length,
                    AttributeType::from($attribute->type),
                    $attribute->identifier,
                    $attribute->required,
                    $attribute->unique,
                    $attribute->make,
                    $attribute->change
                );

                // if enum make the enums
                // todo find a way if the enum already exists (by name?) to avoid doubles

                if ($attribute->type === AttributeType::ENUM->value) {
                    $enumE = Enum::make(
                        $project,
                        $this->enumRepo->nextIdentity(),
                        $domainModelE,
                        $attribute->enum->name,
                    );
                    $enumE->change($domainModelE, $attribute->enum->name, $attribute->enum->namespace);
                    $this->enumRepo->save($enumE);
                    foreach ($attribute->enum->options as $option) {
                        $optionE = EnumOption::make(
                            $enumE,
                            $this->enumOptionRepo->nextIdentity(),
                            $option->code,
                            $option->value
                        );
                        $this->enumOptionRepo->save($optionE);
                    }
                    $attributeE->setEnum($enumE);
                }
                $attributeE->setSequence($attribute->sequence);
                $this->attributeRepo->save($attributeE);
            }
            foreach ($domainModel->associations as $association) {
                $associationE = Association::make(
                    $domainModelE,
                    $this->associationRepo->nextIdentity(),
                    AssociationType::from($association->type),
                    $association->mappedBy,
                    $association->inversedBy,
                    FetchType::from($association->fetch),
                    $association->orderBy,
                    OrderType::from($association->orderDirection),
                    $domainModelE
                );

                // todo import the extra fields (required, make, change) for a relation

                $associationToDo = new \stdClass();
                $associationToDo->relationUuid = $associationE->getUuid();
                $associationToDo->from = $domainModelE->getUuid();
                $associationToDo->to = $association->targetDomainModel->name;
                $associationToDo->relation = $associationE;
                $targetModelList[] = $associationToDo;

                $associationE->setSequence($association->sequence);
                $this->associationRepo->save($associationE);
            }

            // -- make the enums
            // todo import only the orphan enums

            // -- make the queries
            $queries = $this->findQueriesForDomainModel($domainModel->uuid, $projectClass->queries);
            foreach ($queries as $query) {
                $queryE = Query::make(
                    $project,
                    $this->queryRepo->nextIdentity(),
                    $domainModelE,
                    $query->name
                );
                $queryE->change(
                    $domainModelE,
                    $query->name,
                    $query->namespace,
                    $query->mapping
                );
                $this->queryRepo->save($queryE);
            }

            // -- make the commands
            $commands = $this->findCommandForDomainModel($domainModel->uuid, $projectClass->commands);
            foreach ($commands as $command) {
                $commandE = Command::make(
                    $project,
                    $this->commandRepo->nextIdentity(),
                    $domainModelE,
                    $command->name
                );
                $commandE->change(
                    $domainModelE,
                    $command->name,
                    $command->namespace,
                    $command->mapping
                );
                $this->commandRepo->save($commandE);
            }

            $domainModelEList[] = $domainModelE;
        }

        // -- fix the relations

        foreach ($targetModelList as $associationToFix) {
            /* @var Association $relationE */
            $associationE = $associationToFix->relation;
            $targetDomainModelE = $this->findTargetModel($associationToFix->to, $domainModelEList);
            if (!is_null($targetDomainModelE)) {
                $associationE->change(
                    $associationE->getType(),
                    $associationE->getMappedBy(),
                    $associationE->getInversedBy(),
                    $associationE->getFetch(),
                    $associationE->getOrderBy(),
                    $associationE->getOrderDirection(),
                    $targetDomainModelE,
                    $associationE->isMake(),
                    $associationE->isChange(),
                    $associationE->isRequired()
                );
                $this->associationRepo->save($associationE);
            }
        }

        // -- make the templates

        foreach ($projectClass->templates as $template) {
            $templateE = Template::make(
                $project,
                $this->templateRepo->nextIdentity(),
                TemplateType::from($template->type),
                $template->name,
                $template->language
            );
            $templateE->change(
                TemplateType::from($template->type),
                $template->name,
                $template->language,
                $template->template
            );
            $this->templateRepo->save($templateE);
        }

        // -- make the model status list -----------------------------------------
        $listStatus = [];
        foreach ($projectClass->status as $status) {
            $statusE = ModelStatus::make(
                $project,
                $this->modelStatusRepo->nextIdentity(),
                $status->name,
                $status->color,
                $status->isStart,
                $status->isFinal
            );
            $statusE->setSequence($status->sequence);
            $position = new \stdClass();
            $position->x = $status->x;
            $position->y = $status->y;
            $statusE->setWorkflow($position, [], []); // todo fix the workflow with the new IDS

            $this->modelStatusRepo->save($statusE);
            $listStatus[] = $statusE;
        }

        return true;
    }

    private function findModuleByNameAndSequence(string $name, int $sequence, array $source): ?Module
    {
        foreach ($source as $module) {
            if ($module->getName() === $name
                && $module->getSequence() === $sequence) {
                return $module;
            }
        }
        return null;
    }

    private function findTargetModel(string $name, array $source): ?DomainModel
    {
        foreach ($source as $model) {
            if ($model->getName() === $name) {
                return $model;
            }
        }
        return null;
    }

    private function findQueriesForDomainModel(string $uuid, array $queries): array
    {
        $result = [];
        foreach ($queries as $query) {
            if ($query->domainModel->uuid === $uuid) {
                $result[] = $query;
            }
        }
        return $result;
    }

    private function findCommandForDomainModel(string $uuid, array $commands): array
    {
        $result = [];
        foreach ($commands as $command) {
            if ($command->domainModel->uuid === $uuid) {
                $result[] = $command;
            }
        }
        return $result;
    }
}
