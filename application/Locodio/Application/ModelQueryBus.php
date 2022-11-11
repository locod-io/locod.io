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

use App\Locodio\Application\Query\Model\GetCommand;
use App\Locodio\Application\Query\Model\GetDomainModel;
use App\Locodio\Application\Query\Model\GetEnum;
use App\Locodio\Application\Query\Model\GetEnumValues;
use App\Locodio\Application\Query\Model\GetMasterTemplate;
use App\Locodio\Application\Query\Model\GetQuery;
use App\Locodio\Application\Query\Model\GetTemplate;
use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Application\Query\Model\Readmodel\EnumRM;
use App\Locodio\Application\Query\Model\Readmodel\GeneratedCodeRM;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;
use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Application\Security\ModelPermissionService;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ModelQueryBus
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
        protected QueryRepository          $queryRepo,
        protected CommandRepository        $commandRepo,
        protected TemplateRepository       $templateRepo,
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
    // Project
    // ——————————————————————————————————————————————————————————————————————————

    public function getProjectById(int $id): ProjectRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProject = new GetProject($this->projectRepo);
        return $GetProject->ById($id);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Template
    // ——————————————————————————————————————————————————————————————————————————

    public function getTemplateById(int $id): TemplateRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($id);

        $GetTemplate = new GetTemplate(
            $this->templateRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo
        );
        return $GetTemplate->ById($id);
    }

    public function generateTemplateBySubjectId(
        int $id,
        int $subjectId
    ): GeneratedCodeRM {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($id);

        $GetTemplate = new GetTemplate(
            $this->templateRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo
        );
        return $GetTemplate->GenerateBySubjectId($id, $subjectId);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Enum values
    // ——————————————————————————————————————————————————————————————————————————

    public function getEnumValues(): \stdClass
    {
        $this->permission->CheckRole(['ROLE_USER']);

        return GetEnumValues::getModelEnumValues();
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Domain model
    // ——————————————————————————————————————————————————————————————————————————

    public function getDomainModelById(int $id): DomainModelRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($id);

        $GetDomainModel = new GetDomainModel($this->domainModelRepo);
        return $GetDomainModel->ById($id);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Enum
    // ——————————————————————————————————————————————————————————————————————————

    public function getEnumById(int $id): EnumRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumId($id);

        $GetEnum = new GetEnum($this->enumRepo);
        return $GetEnum->ById($id);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Query
    // ——————————————————————————————————————————————————————————————————————————

    public function getQueryById(int $id): QueryRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryId($id);

        $GetQuery = new GetQuery($this->queryRepo);
        return $GetQuery->ById($id);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Command
    // ——————————————————————————————————————————————————————————————————————————

    public function getCommandById(int $id): CommandRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandId($id);

        $GetCommand = new GetCommand($this->commandRepo);
        return $GetCommand->ById($id);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Master Templates
    // ——————————————————————————————————————————————————————————————————————————

    public function getMasterTemplateById(int $id): MasterTemplateRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateId($id);

        $GetMasterTemplate = new GetMasterTemplate($this->masterTemplateRepo, $this->userRepo);
        return $GetMasterTemplate->ById($id);
    }

    public function getPublicTemplates(): MasterTemplateRMCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);

        $GetMasterTemplate = new GetMasterTemplate($this->masterTemplateRepo, $this->userRepo);
        return $GetMasterTemplate->Public();
    }
}
