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

use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProjectHandler;
use App\Locodio\Application\Security\ModelPermissionService;
use App\Locodio\Application\traits\model_association_command;
use App\Locodio\Application\traits\model_attribute_command;
use App\Locodio\Application\traits\model_command_command;
use App\Locodio\Application\traits\model_domain_model_command;
use App\Locodio\Application\traits\model_enum_command;
use App\Locodio\Application\traits\model_enum_option_command;
use App\Locodio\Application\traits\model_master_template_command;
use App\Locodio\Application\traits\model_query_command;
use App\Locodio\Application\traits\model_template_command;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\AssociationRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ModelCommandBus
{
    // -- traits
    use model_template_command;
    use model_master_template_command;
    use model_domain_model_command;
    use model_attribute_command;
    use model_association_command;
    use model_enum_command;
    use model_enum_option_command;
    use model_query_command;
    use model_command_command;

    // -- permission service
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
        protected AttributeRepository      $attributeRepo,
        protected AssociationRepository    $associationRepo,
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
            $this->attributeRepo,
            $this->associationRepo,
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
}
