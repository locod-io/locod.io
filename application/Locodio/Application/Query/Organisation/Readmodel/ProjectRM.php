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

namespace App\Locodio\Application\Query\Organisation\Readmodel;

use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Application\Query\Model\Readmodel\CommandRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\EnumRM;
use App\Locodio\Application\Query\Model\Readmodel\EnumRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModelSettingsRM;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRM;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModuleRM;
use App\Locodio\Application\Query\Model\Readmodel\ModuleRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Application\Query\Model\Readmodel\QueryRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRMCollection;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Lodocio\Application\Query\Project\ReadModel\DocProjectReadModel;

class ProjectRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                      $id,
        protected string                   $uuid,
        protected string                   $code,
        protected string                   $name,
        protected string                   $color,
        protected string                   $domainLayer,
        protected string                   $applicationLayer,
        protected string                   $infrastructureLayer,
        protected string                   $logo,
        protected string                   $slug,
        protected string                   $description,
        protected string                   $gitRepository,
        protected array                    $relatedProjects,
        protected array                    $relatedRoadmaps,
        protected ?ModelSettingsRM         $modelSettings = null,
        protected ?DocProjectReadModel     $docProjectRM = null,
        protected ?OrganisationRM          $organisationRM = null,
        protected ?DomainModelRMCollection $domainModels = null,
        protected ?EnumRMCollection        $enums = null,
        protected ?QueryRMCollection       $queries = null,
        protected ?CommandRMCollection     $commands = null,
        protected ?TemplateRMCollection    $templates = null,
        protected ?ModuleRMCollection      $modules = null,
        protected ?ModelStatusRMCollection $status = null,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(
        Project $model,
        bool    $full = false,
        bool    $simple = false,
    ): self {
        $modelSettingsRM = null;
        if (!is_null($model->getModelSettings())) {
            $modelSettingsRM = ModelSettingsRM::hydrateFromModel($model->getModelSettings());
        }

        if ($full) {
            $templates = new TemplateRMCollection();
            foreach ($model->getTemplates() as $template) {
                $templates->addItem(TemplateRM::hydrateFromModel($template));
            }

            $commands = new CommandRMCollection();
            foreach ($model->getCommands() as $command) {
                $commands->addItem(CommandRM::hydrateFromModel($command));
            }

            $queries = new QueryRMCollection();
            foreach ($model->getQueries() as $query) {
                $queries->addItem(QueryRM::hydrateFromModel($query));
            }

            $enums = new EnumRMCollection();
            foreach ($model->getEnums() as $enum) {
                $enums->addItem(EnumRM::hydrateFromModel($enum));
            }

            $domainModels = new DomainModelRMCollection();
            foreach ($model->getDomainModels() as $domainModel) {
                $domainModels->addItem(DomainModelRM::hydrateFromModel($domainModel, true));
            }

            $modules = new ModuleRMCollection();
            foreach ($model->getModules() as $module) {
                $modules->addItem(ModuleRM::hydrateFromModel($module));
            }

            $modelStatus = new ModelStatusRMCollection();
            foreach ($model->getModelStatus() as $status) {
                $modelStatus->addItem(ModelStatusRM::hydrateFromModel($status, true));
            }

            $docProjectRM = null;
            if (!is_null($model->getDocProject())) {
                $docProjectRM = DocProjectReadModel::hydrateFromModel($model->getDocProject());
            }

            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getDomainLayer(),
                $model->getApplicationLayer(),
                $model->getInfrastructureLayer(),
                $model->getLogo(),
                $model->getSlug(),
                $model->getDescription(),
                $model->getGitRepository(),
                $model->getRelatedProjects(),
                $model->getRelatedRoadmaps(),
                $modelSettingsRM,
                $docProjectRM,
                OrganisationRM::hydrateFromModel($model->getOrganisation()),
                $domainModels,
                $enums,
                $queries,
                $commands,
                $templates,
                $modules,
                $modelStatus,
            );
        } else {
            if ($simple) {
                return new self(
                    $model->getId(),
                    $model->getUuidAsString(),
                    $model->getCode(),
                    $model->getName(),
                    $model->getColor(),
                    $model->getDomainLayer(),
                    $model->getApplicationLayer(),
                    $model->getInfrastructureLayer(),
                    $model->getLogo(),
                    $model->getSlug(),
                    $model->getDescription(),
                    $model->getGitRepository(),
                    $model->getRelatedProjects(),
                    $model->getRelatedRoadmaps()
                );
            } else {

                $docProjectRM = null;
                if (!is_null($model->getDocProject())) {
                    $docProjectRM = DocProjectReadModel::hydrateFromModel($model->getDocProject());
                }

                return new self(
                    $model->getId(),
                    $model->getUuidAsString(),
                    $model->getCode(),
                    $model->getName(),
                    $model->getColor(),
                    $model->getDomainLayer(),
                    $model->getApplicationLayer(),
                    $model->getInfrastructureLayer(),
                    $model->getLogo(),
                    $model->getSlug(),
                    $model->getDescription(),
                    $model->getGitRepository(),
                    $model->getRelatedProjects(),
                    $model->getRelatedRoadmaps(),
                    $modelSettingsRM,
                    $docProjectRM,
                );
            }
        }
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->code = $this->getCode();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->domainLayer = $this->getDomainLayer();
        $json->applicationLayer = $this->getApplicationLayer();
        $json->infrastructureLayer = $this->getInfrastructureLayer();
        $json->logo = str_replace(dirname($this->getLogo()), '', $this->getLogo());
        $json->slug = $this->getSlug();
        $json->description = $this->getDescription();
        $json->gitRepository = $this->getGitRepository();
        $json->relatedRoadmaps = $this->getRelatedRoadmaps();
        $json->relatedProjects = $this->getRelatedProjects();

        $json->modelSettings = $this->getModelSettings();

        if (!is_null($this->getDocProjectRM())) {
            $json->docProject = $this->getDocProjectRM();
        }
        if (!is_null($this->getOrganisationRM())) {
            $json->organisation = $this->getOrganisationRM();
        }
        if (!is_null($this->getModules())) {
            $json->modules = $this->getModules()->getCollection();
        }
        if (!is_null($this->getStatus())) {
            $json->status = $this->getStatus()->getCollection();
        }
        if (!is_null($this->getDomainModels())) {
            $json->domainModels = $this->getDomainModels()->getCollection();
        }
        if (!is_null($this->getEnums())) {
            $json->enums = $this->getEnums()->getCollection();
        }
        if (!is_null($this->getQueries())) {
            $json->queries = $this->getQueries()->getCollection();
        }
        if (!is_null($this->getCommands())) {
            $json->commands = $this->getCommands()->getCollection();
        }
        if (!is_null($this->getTemplates())) {
            $json->templates = $this->getTemplates()->getCollection();
        }
        return $json;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Setters
    // ——————————————————————————————————————————————————————————————————————————

    public function setModuleCollection(ModuleRMCollection $modules): void
    {
        $this->modules = $modules;
    }

    public function setStatusCollection(ModelStatusRMCollection $status): void
    {
        $this->status = $status;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getOrganisationRM(): ?OrganisationRM
    {
        return $this->organisationRM;
    }

    public function getDomainModels(): ?DomainModelRMCollection
    {
        return $this->domainModels;
    }

    public function getEnums(): ?EnumRMCollection
    {
        return $this->enums;
    }

    public function getQueries(): ?QueryRMCollection
    {
        return $this->queries;
    }

    public function getCommands(): ?CommandRMCollection
    {
        return $this->commands;
    }

    public function getTemplates(): ?TemplateRMCollection
    {
        return $this->templates;
    }

    public function getDomainLayer(): string
    {
        return $this->domainLayer;
    }

    public function getApplicationLayer(): string
    {
        return $this->applicationLayer;
    }

    public function getInfrastructureLayer(): string
    {
        return $this->infrastructureLayer;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getModelSettings(): ?ModelSettingsRM
    {
        return $this->modelSettings;
    }

    public function getModules(): ?ModuleRMCollection
    {
        return $this->modules;
    }

    public function getStatus(): ?ModelStatusRMCollection
    {
        return $this->status;
    }

    public function getDocProjectRM(): ?DocProjectReadModel
    {
        return $this->docProjectRM;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRelatedProjects(): array
    {
        return $this->relatedProjects;
    }

    public function getRelatedRoadmaps(): array
    {
        return $this->relatedRoadmaps;
    }

    public function getGitRepository(): string
    {
        return $this->gitRepository;
    }

}
