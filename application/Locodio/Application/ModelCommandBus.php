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

namespace App\Locodio\Application;

use App\Locodio\Application\Command\Model\AddCommand\AddCommand;
use App\Locodio\Application\Command\Model\AddCommand\AddCommandHandler;
use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModel;
use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModelHandler;
use App\Locodio\Application\Command\Model\AddEnum\AddEnum;
use App\Locodio\Application\Command\Model\AddEnum\AddEnumHandler;
use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOption;
use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOptionHandler;
use App\Locodio\Application\Command\Model\AddField\AddField;
use App\Locodio\Application\Command\Model\AddField\AddFieldHandler;
use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplate;
use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplateHandler;
use App\Locodio\Application\Command\Model\AddQuery\AddQuery;
use App\Locodio\Application\Command\Model\AddQuery\AddQueryHandler;
use App\Locodio\Application\Command\Model\AddRelation\AddRelation;
use App\Locodio\Application\Command\Model\AddRelation\AddRelationHandler;
use App\Locodio\Application\Command\Model\AddTemplate\AddTemplate;
use App\Locodio\Application\Command\Model\AddTemplate\AddTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeCommand\ChangeCommand;
use App\Locodio\Application\Command\Model\ChangeCommand\ChangeCommandHandler;
use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModel;
use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModelHandler;
use App\Locodio\Application\Command\Model\ChangeEnum\ChangeEnum;
use App\Locodio\Application\Command\Model\ChangeEnum\ChangeEnumHandler;
use App\Locodio\Application\Command\Model\ChangeEnumOption\ChangeEnumOption;
use App\Locodio\Application\Command\Model\ChangeEnumOption\ChangeEnumOptionHandler;
use App\Locodio\Application\Command\Model\ChangeField\ChangeField;
use App\Locodio\Application\Command\Model\ChangeField\ChangeFieldHandler;
use App\Locodio\Application\Command\Model\ChangeMasterTemplate\ChangeMasterTemplate;
use App\Locodio\Application\Command\Model\ChangeMasterTemplate\ChangeMasterTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeQuery\ChangeQuery;
use App\Locodio\Application\Command\Model\ChangeQuery\ChangeQueryHandler;
use App\Locodio\Application\Command\Model\ChangeRelation\ChangeRelation;
use App\Locodio\Application\Command\Model\ChangeRelation\ChangeRelationHandler;
use App\Locodio\Application\Command\Model\ChangeTemplate\ChangeTemplate;
use App\Locodio\Application\Command\Model\ChangeTemplate\ChangeTemplateHandler;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProjectHandler;
use App\Locodio\Application\Command\Model\DeleteCommand\DeleteCommand;
use App\Locodio\Application\Command\Model\DeleteCommand\DeleteCommandHandler;
use App\Locodio\Application\Command\Model\DeleteDomainModel\DeleteDomainModel;
use App\Locodio\Application\Command\Model\DeleteDomainModel\DeleteDomainModelHandler;
use App\Locodio\Application\Command\Model\DeleteEnum\DeleteEnum;
use App\Locodio\Application\Command\Model\DeleteEnum\DeleteEnumHandler;
use App\Locodio\Application\Command\Model\DeleteEnumOption\DeleteEnumOption;
use App\Locodio\Application\Command\Model\DeleteEnumOption\DeleteEnumOptionHandler;
use App\Locodio\Application\Command\Model\DeleteField\DeleteField;
use App\Locodio\Application\Command\Model\DeleteField\DeleteFieldHandler;
use App\Locodio\Application\Command\Model\DeleteMasterTemplate\DeleteMasterTemplate;
use App\Locodio\Application\Command\Model\DeleteMasterTemplate\DeleteMasterTemplateHandler;
use App\Locodio\Application\Command\Model\DeleteQuery\DeleteQuery;
use App\Locodio\Application\Command\Model\DeleteQuery\DeleteQueryHandler;
use App\Locodio\Application\Command\Model\DeleteRelation\DeleteRelation;
use App\Locodio\Application\Command\Model\DeleteRelation\DeleteRelationHandler;
use App\Locodio\Application\Command\Model\DeleteTemplate\DeleteTemplate;
use App\Locodio\Application\Command\Model\DeleteTemplate\DeleteTemplateHandler;
use App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate\ExportTemplateToMasterTemplate;
use App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate\ExportTemplateToMasterTemplateHandler;
use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplates;
use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplatesHandler;
use App\Locodio\Application\Command\Model\OrderCommand\OrderCommand;
use App\Locodio\Application\Command\Model\OrderCommand\OrderCommandHandler;
use App\Locodio\Application\Command\Model\OrderDomainModel\OrderDomainModel;
use App\Locodio\Application\Command\Model\OrderDomainModel\OrderDomainModelHandler;
use App\Locodio\Application\Command\Model\OrderEnum\OrderEnum;
use App\Locodio\Application\Command\Model\OrderEnum\OrderEnumHandler;
use App\Locodio\Application\Command\Model\OrderEnumOption\OrderEnumOption;
use App\Locodio\Application\Command\Model\OrderEnumOption\OrderEnumOptionHandler;
use App\Locodio\Application\Command\Model\OrderField\OrderField;
use App\Locodio\Application\Command\Model\OrderField\OrderFieldHandler;
use App\Locodio\Application\Command\Model\OrderMasterTemplate\OrderMasterTemplate;
use App\Locodio\Application\Command\Model\OrderMasterTemplate\OrderMasterTemplateHandler;
use App\Locodio\Application\Command\Model\OrderQuery\OrderQuery;
use App\Locodio\Application\Command\Model\OrderQuery\OrderQueryHandler;
use App\Locodio\Application\Command\Model\OrderRelation\OrderRelation;
use App\Locodio\Application\Command\Model\OrderRelation\OrderRelationHandler;
use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplate;
use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplateHandler;
use App\Locodio\Application\Security\ModelPermissionService;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\FieldRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\RelationRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ModelCommandBus
{
    protected ModelPermissionService $permission;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                 $security,
        protected EntityManagerInterface   $entityManager,
        protected bool                     $isolationMode,
        protected ProjectRepository        $projectRepo,
        protected DomainModelRepository    $domainModelRepo,
        protected EnumRepository           $enumRepo,
        protected EnumOptionRepository     $enumOptionRepo,
        protected QueryRepository          $queryRepo,
        protected CommandRepository        $commandRepo,
        protected TemplateRepository       $templateRepo,
        protected FieldRepository          $fieldRepo,
        protected RelationRepository       $relationRepo,
        protected MasterTemplateRepository $masterTemplateRepo,
        protected UserRepository           $userRepo
    ) {
        $this->permission = new ModelPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Create example project
    // ——————————————————————————————————————————————————————————————————————————

    public function createExampleProject(int $projectId): bool
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($projectId);
        $command = new CreateSampleProject($projectId);
        $createSampleProjectHandler = new CreateSampleProjectHandler(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->fieldRepo,
            $this->relationRepo,
            $this->enumRepo,
            $this->enumOptionRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->templateRepo
        );
        $result = $createSampleProjectHandler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Template
    // ——————————————————————————————————————————————————————————————————————————

    public function addTemplate(\stdClass $jsonCommand): bool
    {
        $command = AddTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new AddTemplateHandler($this->projectRepo, $this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeTemplate(\stdClass $jsonCommand): bool
    {
        $command = ChangeTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($command->getId());

        $handler = new ChangeTemplateHandler($this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderTemplate(\stdClass $jsonCommand): bool
    {
        $command = OrderTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateIds($command->getSequence());

        $handler = new OrderTemplateHandler($this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteTemplate(\stdClass $jsonCommand): bool
    {
        $command = DeleteTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($command->getId());

        $handler = new DeleteTemplateHandler($this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function importTemplatesFromMasterTemplates(\stdClass $jsonCommand): bool
    {
        $command = ImportTemplatesFromMasterTemplates::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckMasterTemplateIds($command->getMasterTemplateIds());

        $handler = new ImportTemplatesFromMasterTemplatesHandler(
            $this->projectRepo,
            $this->masterTemplateRepo,
            $this->templateRepo
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function exportTemplateToMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = ExportTemplateToMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());
        $this->permission->CheckTemplateId($command->getTemplateId());

        $handler =new ExportTemplateToMasterTemplateHandler(
            $this->userRepo,
            $this->masterTemplateRepo,
            $this->templateRepo
        );

        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Master Template
    // ——————————————————————————————————————————————————————————————————————————

    public function addMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = AddMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());

        $handler = new AddMasterTemplateHandler($this->userRepo, $this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = ChangeMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateId($command->getId());

        $handler = new ChangeMasterTemplateHandler($this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = DeleteMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateId($command->getId());

        $handler = new DeleteMasterTemplateHandler($this->templateRepo, $this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = OrderMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateIds($command->getSequence());

        $handler = new OrderMasterTemplateHandler($this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Domain model
    // ——————————————————————————————————————————————————————————————————————————

    public function addDomainModel(\stdClass $jsonCommand): bool
    {
        $command = AddDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new AddDomainModelHandler($this->projectRepo, $this->domainModelRepo, $this->fieldRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeDomainModel(\stdClass $jsonCommand): bool
    {
        $command = ChangeDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($command->getId());

        $handler = new ChangeDomainModelHandler($this->domainModelRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderDomainModel(\stdClass $jsonCommand): bool
    {
        $command = OrderDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelIds($command->getSequence());

        $handler = new OrderDomainModelHandler($this->domainModelRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteDomainModel(\stdClass $jsonCommand): bool
    {
        $command = DeleteDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($command->getId());

        $handler = new DeleteDomainModelHandler($this->domainModelRepo, $this->fieldRepo, $this->relationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }


    public function addField(\stdClass $jsonCommand): bool
    {
        $command = AddField::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddFieldHandler($this->domainModelRepo, $this->fieldRepo, $this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeField(\stdClass $jsonCommand): bool
    {
        $command = ChangeField::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckFieldId($command->getId());

        $handler = new ChangeFieldHandler($this->fieldRepo, $this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderField(\stdClass $jsonCommand): bool
    {
        $command = OrderField::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckFieldIds($command->getSequence());

        $handler = new OrderFieldHandler($this->fieldRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteField(\stdClass $jsonCommand): bool
    {
        $command = DeleteField::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckFieldId($command->getId());

        $handler = new DeleteFieldHandler($this->fieldRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function addRelation(\stdClass $jsonCommand): bool
    {
        $command = AddRelation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddRelationHandler($this->domainModelRepo, $this->relationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeRelation(\stdClass $jsonCommand): bool
    {
        $command = ChangeRelation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckRelationId($command->getId());

        $handler = new ChangeRelationHandler($this->domainModelRepo, $this->relationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderRelation(\stdClass $jsonCommand): bool
    {
        $command = OrderRelation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckRelationIds($command->getSequence());

        $handler = new OrderRelationHandler($this->relationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteRelation(\stdClass $jsonCommand): bool
    {
        $command = DeleteRelation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckRelationId($command->getId());

        $handler = new DeleteRelationHandler($this->relationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Enum
    // ——————————————————————————————————————————————————————————————————————————

    public function addEnum(\stdClass $jsonCommand): bool
    {
        $command = AddEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddEnumHandler(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->enumOptionRepo
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeEnum(\stdClass $jsonCommand): bool
    {
        $command = ChangeEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumId($command->getId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new ChangeEnumHandler($this->domainModelRepo, $this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderEnum(\stdClass $jsonCommand): bool
    {
        $command = OrderEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumIds($command->getSequence());

        $handler = new OrderEnumHandler($this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteEnum(\stdClass $jsonCommand): bool
    {
        $command = DeleteEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumId($command->getId());

        $handler = new DeleteEnumHandler($this->enumRepo, $this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function addEnumOption(\stdClass $jsonCommand): bool
    {
        $command = AddEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumId($command->getEnumId());

        $handler = new AddEnumOptionHandler($this->enumRepo, $this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeEnumOption(\stdClass $jsonCommand): bool
    {
        $command = ChangeEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumOptionId($command->getId());

        $handler = new ChangeEnumOptionHandler($this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderEnumOption(\stdClass $jsonCommand): bool
    {
        $command = OrderEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumOptionIds($command->getSequence());

        $handler = new OrderEnumOptionHandler($this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteEnumOption(\stdClass $jsonCommand): bool
    {
        $command = DeleteEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumOptionId($command->getId());

        $handler = new DeleteEnumOptionHandler($this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Query
    // ——————————————————————————————————————————————————————————————————————————

    public function addQuery(\stdClass $jsonCommand): bool
    {
        $command = AddQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddQueryHandler($this->projectRepo, $this->domainModelRepo, $this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeQuery(\stdClass $jsonCommand): bool
    {
        $command = ChangeQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryId($command->getId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new ChangeQueryHandler($this->domainModelRepo, $this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderQuery(\stdClass $jsonCommand): bool
    {
        $command = OrderQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryIds($command->getSequence());

        $handler = new OrderQueryHandler($this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteQuery(\stdClass $jsonCommand): bool
    {
        $command = DeleteQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryId($command->getId());

        $handler = new DeleteQueryHandler($this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Command
    // ——————————————————————————————————————————————————————————————————————————

    public function addCommand(\stdClass $jsonCommand): bool
    {
        $command = AddCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddCommandHandler($this->projectRepo, $this->domainModelRepo, $this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeCommand(\stdClass $jsonCommand): bool
    {
        $command = ChangeCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandId($command->getId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new ChangeCommandHandler($this->domainModelRepo, $this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderCommand(\stdClass $jsonCommand): bool
    {
        $command = OrderCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandIds($command->getSequence());

        $handler = new OrderCommandHandler($this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteCommand(\stdClass $jsonCommand): bool
    {
        $command = DeleteCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandId($command->getId());

        $handler = new DeleteCommandHandler($this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
