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

namespace App\Tests\integration\application\Locodio\Security;

use App\Linear\Application\Query\LinearConfig;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProjectHandler;
use App\Locodio\Application\Query\Model\GetDocumentor;
use App\Locodio\Application\Security\ModelPermissionService;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class ModelPermissionServiceTest extends DatabaseTestCase
{
    protected const NOT_ALLOWED_DATA = 'Action not allowed for this user.';

    public function setUp(): void
    {
        parent::setUp();
    }

    // -- create project stack ---------------------------------------------------------------

    private function createProjectStack(string $email): Project
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $organisationRepo = $this->entityManager->getRepository(Organisation::class);

        $project = $modelFactory->makeProject(Uuid::v4());
        $user = $modelFactory->makeUser($email);
        $project->getOrganisation()->addUser($user);
        $organisationRepo->save($project->getOrganisation());
        $this->entityManager->flush();

        $createSampleProjectHandler = new CreateSampleProjectHandler(
            $this->entityManager->getRepository(Project::class),
            $this->entityManager->getRepository(DomainModel::class),
            $this->entityManager->getRepository(Attribute::class),
            $this->entityManager->getRepository(Association::class),
            $this->entityManager->getRepository(Enum::class),
            $this->entityManager->getRepository(EnumOption::class),
            $this->entityManager->getRepository(Query::class),
            $this->entityManager->getRepository(Command::class),
            $this->entityManager->getRepository(Template::class),
            $this->entityManager->getRepository(ModelStatus::class),
            $this->entityManager->getRepository(Module::class),
            $this->entityManager->getRepository(ModelSettings::class),
        );
        $command = new CreateSampleProject($project->getUuid()->toRfc4122());
        $createSampleProjectHandler->go($command);
        $this->entityManager->flush();

        return $project;
    }

    // -- tests ------------------------------------------------------------------------------

    public function testModelPermissionServiceWithUser(): \stdClass
    {
        $dummyLinearConfig = new LinearConfig('', '', '');
        $email = 'testModelSecurityService@test.com';
        $project = $this->createProjectStack($email);
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->getByEmail($email);
        $user->addOrganisation($project->getOrganisation());

        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckUserId($user->getId());
        $permissionService->CheckOrganisationId($project->getOrganisation()->getId());
        $permissionService->CheckProjectId($project->getId());
        $permissionService->CheckRole(['ROLE_USER']);

        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $associationRepo = $this->entityManager->getRepository(Association::class);
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);
        $queryRepo = $this->entityManager->getRepository(Query::class);
        $commandRepo = $this->entityManager->getRepository(Command::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);
        $modelStatusRepo = $this->entityManager->getRepository(ModelStatus::class);
        $moduleRepo = $this->entityManager->getRepository(Module::class);

        /** @var DomainModel $domainModel */
        $domainModel = $domainModelRepo->getByProject($project)[0];
        /** @var Attribute $attributes */
        $attribute = $attributeRepo->getByDomainModel($domainModel)[0];
        /** @var Association $association */
        $association = $associationRepo->getByDomainModel($domainModel)[0];
        /** @var Enum $enum */
        $enum = $enumRepo->getByProject($project)[0];
        /** @var $enumOption $enumOption */
        $enumOption = $enumOptionRepo->getByEnum($enum)[0];
        /** @var Query $query */
        $query = $queryRepo->getByProject($project)[0];
        /** @var Command $command */
        $command = $commandRepo->getByProject($project)[0];
        /** @var Template $template */
        $template = $templateRepo->getByProject($project)[0];
        /** @var ModelStatus $modelStatus */
        $modelStatus = $modelStatusRepo->getByProject($project)[0];
        /** @var Module $module */
        $module = $moduleRepo->getByProject($project)[0];

        // -- check the permissions of the models
        $permissionService->CheckDomainModelId($domainModel->getId());
        $permissionService->CheckAssociationId($association->getId());
        $permissionService->CheckAttributeId($attribute->getId());
        $permissionService->CheckEnumId($enum->getId());
        $permissionService->CheckEnumOptionId($enumOption->getId());
        $permissionService->CheckCommandId($command->getId());
        $permissionService->CheckQueryId($query->getId());
        $permissionService->CheckModuleId($module->getId());
        $permissionService->CheckModelStatusId($modelStatus->getId());

        // -- check the type of the models
        Assert::assertInstanceOf(User::class, $user);
        Assert::assertInstanceOf(Project::class, $project);
        Assert::assertInstanceOf(Organisation::class, $project->getOrganisation());
        Assert::assertInstanceOf(ModelSettings::class, $project->getModelSettings());
        Assert::assertInstanceOf(DomainModel::class, $domainModel);
        Assert::assertInstanceOf(Attribute::class, $attribute);
        Assert::assertInstanceOf(Association::class, $association);
        Assert::assertInstanceOf(Enum::class, $enum);
        Assert::assertInstanceOf(EnumOption::class, $enumOption);
        Assert::assertInstanceOf(Query::class, $query);
        Assert::assertInstanceOf(Command::class, $command);
        Assert::assertInstanceOf(Template::class, $template);
        Assert::assertInstanceOf(ModelStatus::class, $modelStatus);
        Assert::assertInstanceOf(Module::class, $module);

        // -- make some documentors ----------------------------------------------------------
        $getDocumentor = new GetDocumentor(
            $this->entityManager,
            $this->entityManager->getRepository(Documentor::class),
            $moduleRepo,
            $domainModelRepo,
            $enumRepo,
            $queryRepo,
            $commandRepo,
            $modelStatusRepo,
            $dummyLinearConfig,
        );

        $documentorModule = $getDocumentor->ByTypeAndSubjectId(DocumentorType::MODULE, $module->getId());
        $documentorDomainModel = $getDocumentor->ByTypeAndSubjectId(DocumentorType::DOMAIN_MODEL, $domainModel->getId());
        $documentorEnum = $getDocumentor->ByTypeAndSubjectId(DocumentorType::ENUM, $enum->getId());
        $documentorQuery = $getDocumentor->ByTypeAndSubjectId(DocumentorType::QUERY, $query->getId());
        $documentorCommand = $getDocumentor->ByTypeAndSubjectId(DocumentorType::COMMAND, $command->getId());
        $this->entityManager->flush();

        $permissionService->CheckDocumentorId($documentorModule->getId());
        $permissionService->CheckDocumentorId($documentorDomainModel->getId());
        $permissionService->CheckDocumentorId($documentorEnum->getId());
        $permissionService->CheckDocumentorId($documentorQuery->getId());
        $permissionService->CheckDocumentorId($documentorCommand->getId());

        Assert::assertEquals(DocumentorType::MODULE->value, $documentorModule->getType());
        Assert::assertEquals(DocumentorType::DOMAIN_MODEL->value, $documentorDomainModel->getType());
        Assert::assertEquals(DocumentorType::ENUM->value, $documentorEnum->getType());
        Assert::assertEquals(DocumentorType::QUERY->value, $documentorQuery->getType());
        Assert::assertEquals(DocumentorType::COMMAND->value, $documentorCommand->getType());

        $result = new \stdClass();
        $result->domainModelId = $domainModel->getId();
        $result->associationId = $association->getId();
        $result->attributeId = $attribute->getId();
        $result->enumId = $enum->getId();
        $result->enumOptionId = $enumOption->getId();
        $result->commandId = $command->getId();
        $result->queryId = $query->getId();
        $result->moduleId = $module->getId();
        $result->modelStatusId = $modelStatus->getId();
        // -- some documentor id's
        $result->documentorModuleId = $documentorModule->getId();
        $result->documentorDomainModelId = $documentorDomainModel->getId();
        $result->documentorEnumId = $documentorEnum->getId();
        $result->documentorQueryId = $documentorQuery->getId();
        $result->documentorCommandId = $documentorCommand->getId();

        return $result;
    }

    // -- test login function that should fail ----------------------------------------

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_DomainModel_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_DomainModel@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckDomainModelId($ids->domainModelId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_Association_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_Assiocation@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckAssociationId($ids->associationId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_Attribute_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_Attribute@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckAttributeId($ids->attributeId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_Enum_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_Enum@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckEnumId($ids->enumId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_EnumOption_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_EnumOption@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckEnumOptionId($ids->enumOptionId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_Query_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_Query@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckQueryId($ids->queryId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_Command_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_Command@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckCommandId($ids->queryId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_Module_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_Module@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckModuleId($ids->moduleId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_ModeStatus_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_ModelStatus@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckModelStatusId($ids->modelStatusId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_DocumentorModule_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_DocumentorModule@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckDocumentorId($ids->documentorModuleId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_DocumentorDomainModel_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_DocumentorDomainModel@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckDocumentorId($ids->documentorDomainModelId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_DocumentorEnum_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_DocumentorEnum@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckDocumentorId($ids->documentorEnumId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_DocumentorCommand_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_DocumentorCommand@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckDocumentorId($ids->documentorCommandId);
    }

    /** @depends testModelPermissionServiceWithUser */
    public function testModelPermissionService_DocumentorQuery_NOK(\stdClass $ids): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $user = $modelFactory->makeUser('testModelSecurityService_DocumentorQuery@test.com');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckDocumentorId($ids->documentorQueryId);
    }

    // -- master templates -------------------------------------------------------------

    public function testModelPermissionServiceWithUser_MasterTemplates_OK(): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $user = $modelFactory->makeUser('testModelSecurityService_1@test.com');
        $masterTemplate = MasterTemplate::make(
            $user,
            $masterTemplateRepo->nextIdentity(),
            TemplateType::DOMAIN_MODEL,
            'name',
            'language',
        );
        $masterTemplateRepo->save($masterTemplate);
        $this->entityManager->flush();

        $permissionService = new ModelPermissionService($user, $this->entityManager);
        $permissionService->CheckMasterTemplateId($masterTemplate->getId());

        Assert::assertInstanceOf(MasterTemplate::class, $masterTemplate);
    }

    public function testModelPermissionServiceWithUser_MasterTemplates_NOK()
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $user1 = $modelFactory->makeUser('testModelSecurityService_2@test.com');
        $user2 = $modelFactory->makeUser('testModelSecurityService_3@test.com');
        $masterTemplate = MasterTemplate::make(
            $user1,
            $masterTemplateRepo->nextIdentity(),
            TemplateType::DOMAIN_MODEL,
            'name',
            'language',
        );
        $masterTemplateRepo->save($masterTemplate);
        $this->entityManager->flush();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(self::NOT_ALLOWED_DATA);
        $permissionService = new ModelPermissionService($user2, $this->entityManager);
        $permissionService->CheckMasterTemplateId($masterTemplate->getId());
    }
}
