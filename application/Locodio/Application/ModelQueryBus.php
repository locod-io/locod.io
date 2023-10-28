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

use App\Locodio\Application\Query\Linear\LinearConfig;
use App\Locodio\Application\Query\Linear\traits\linear_queries;
use App\Locodio\Application\Query\Model\GetDocumentation;
use App\Locodio\Application\Query\Model\Readmodel\ProjectDocumentation;
use App\Locodio\Application\Security\ModelPermissionService;
use App\Locodio\Application\traits\model_command_query;
use App\Locodio\Application\traits\model_documentor_query;
use App\Locodio\Application\traits\model_domain_model_query;
use App\Locodio\Application\traits\model_enum_query;
use App\Locodio\Application\traits\model_master_template_query;
use App\Locodio\Application\traits\model_module_query;
use App\Locodio\Application\traits\model_query_query;
use App\Locodio\Application\traits\model_settings_query;
use App\Locodio\Application\traits\model_status_query;
use App\Locodio\Application\traits\model_template_query;
use App\Locodio\Application\traits\organisation_project_query;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ModelQueryBus
{
    // -- traits
    use organisation_project_query;
    use model_domain_model_query;
    use model_enum_query;
    use model_query_query;
    use model_command_query;
    use model_template_query;
    use model_master_template_query;
    use model_status_query;
    use model_settings_query;
    use model_module_query;
    use model_status_query;
    use model_documentor_query;
    use linear_queries;

    // -- permission service
    protected ModelPermissionService $permission;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                 $security,
        protected EntityManagerInterface   $entityManager,
        protected bool                     $isolationMode,
        protected OrganisationRepository   $organisationRepo,
        protected ProjectRepository        $projectRepo,
        protected DomainModelRepository    $domainModelRepo,
        protected EnumRepository           $enumRepo,
        protected QueryRepository          $queryRepo,
        protected CommandRepository        $commandRepo,
        protected TemplateRepository       $templateRepo,
        protected MasterTemplateRepository $masterTemplateRepo,
        protected UserRepository           $userRepo,
        protected ModelStatusRepository    $modelStatusRepo,
        protected ModuleRepository         $moduleRepository,
        protected DocumentorRepository     $documentorRepository,
        protected Environment              $twig,
        protected string                   $uploadFolder,
        protected LinearConfig             $linearConfig,
    ) {
        $this->permission = new ModelPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Project Documentation
    // ——————————————————————————————————————————————————————————————————————————

    public function getProjectDocumentation(int $id): ProjectDocumentation
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProjectDocumentation = new GetDocumentation(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->twig,
            $this->uploadFolder,
        );

        return $GetProjectDocumentation->ByProjectId($id);
    }

    public function downloadProjectDocumentation(int $id): void
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProjectDocumentation = new GetDocumentation(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->twig,
            $this->uploadFolder,
        );

        $GetProjectDocumentation->DownloadDocumentionByProjectId($id);
    }
}
