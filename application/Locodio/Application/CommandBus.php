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

use App\Locodio\Application\Command\Model\ForkTemplate\ForkTemplate;
use App\Locodio\Application\Command\Model\ForkTemplate\ForkTemplateHandler;
use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisation;
use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisationHandler;
use App\Locodio\Application\Command\Organisation\AddProject\AddProject;
use App\Locodio\Application\Command\Organisation\AddProject\AddProjectHandler;
use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisation;
use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisationHandler;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProject;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProjectHandler;
use App\Locodio\Application\Command\Organisation\ChangeProjectDescription\ChangeProjectDescriptionTrait;
use App\Locodio\Application\Command\Organisation\ChangeRelatedProjects\ChangeRelatedProjectsTrait;
use App\Locodio\Application\Command\Organisation\ChangeRelatedRoadmaps\ChangeRelatedRoadmapsTrait;
use App\Locodio\Application\Command\Organisation\OrderOrganisation\OrderOrganisation;
use App\Locodio\Application\Command\Organisation\OrderOrganisation\OrderOrganisationHandler;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProject;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProjectHandler;
use App\Locodio\Application\Command\User\ChangePassword\ChangePassword;
use App\Locodio\Application\Command\User\ChangePassword\ChangePasswordHandler;
use App\Locodio\Application\Command\User\ChangeProfile\ChangeProfile;
use App\Locodio\Application\Command\User\ChangeProfile\ChangeProfileHandler;
use App\Locodio\Application\Command\User\CreateAccount\CreateAccount;
use App\Locodio\Application\Command\User\CreateAccount\CreateAccountHandler;
use App\Locodio\Application\Command\User\ForgotPassword\ForgotPassword;
use App\Locodio\Application\Command\User\ForgotPassword\ForgotPasswordHandler;
use App\Locodio\Application\Command\User\Register\Register;
use App\Locodio\Application\Command\User\Register\RegisterHandler;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHandler;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHash;
use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Application\traits\organisation_organisation_command;
use App\Locodio\Application\traits\organisation_project_command;
use App\Locodio\Application\traits\user_user_command;
use App\Locodio\Domain\Model\Model\MasterTemplateForkRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRegistrationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class CommandBus
{
    // -- traits
    use organisation_organisation_command;
    use organisation_project_command;
    use user_user_command;
    use ChangeProjectDescriptionTrait;
    use ChangeRelatedProjectsTrait;
    use ChangeRelatedRoadmapsTrait;

    // -- permission service
    protected BasePermissionService $permission;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                       $security,
        protected EntityManagerInterface         $entityManager,
        protected UserPasswordHasherInterface    $passwordEncoder,
        protected bool                           $isolationMode,
        protected UserRepository                 $userRepository,
        protected PasswordResetLinkRepository    $passwordResetLinkRepository,
        protected OrganisationRepository         $organisationRepository,
        protected ProjectRepository              $projectRepository,
        protected UserRegistrationLinkRepository $userRegistrationLinkRepository,
        protected MasterTemplateRepository       $masterTemplateRepository,
        protected MasterTemplateForkRepository   $masterTemplateForkRepository,
        protected DocProjectRepository           $docProjectRepository
    ) {
        $this->permission = new BasePermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Registration
    // ——————————————————————————————————————————————————————————————————————————

    public function registerAction(Register $command): \stdClass
    {
        $registerHandler = new RegisterHandler(
            $this->userRegistrationLinkRepository,
            $this->userRepository,
            $this->passwordEncoder
        );
        $result = $registerHandler->Register($command);
        $this->entityManager->flush();
        return $result;
    }

    public function createAccount(CreateAccount $command): \stdClass
    {
        $createAccountHandler = new CreateAccountHandler(
            $this->userRegistrationLinkRepository,
            $this->userRepository,
            $this->organisationRepository,
            $this->projectRepository
        );
        $result = $createAccountHandler->CreateAccount($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— User
    // ——————————————————————————————————————————————————————————————————————————

    public function userForgotPassword(ForgotPassword $command): \stdClass
    {
        $handler = new ForgotPasswordHandler($this->userRepository, $this->passwordResetLinkRepository);
        $result = $handler->ForgotPassword($command);
        $this->entityManager->flush();
        return $result;
    }

    public function resetPasswordWithHash($jsonCommand): bool
    {
        $command = ResetPasswordHash::hydrateFromJson($jsonCommand);
        $handler = new ResetPasswordHandler($this->passwordEncoder, $this->userRepository, $this->passwordResetLinkRepository);
        $handler->UserResetPasswordHash($command);
        $this->entityManager->flush();
        return true;
    }

    public function changePassword($jsonCommand): bool
    {
        $command = ChangePassword::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());

        $handler = new ChangePasswordHandler($this->userRepository, $this->passwordEncoder);
        $handler->go($command);
        $this->entityManager->flush();
        return true;
    }

    public function changeProfile($jsonCommand): bool
    {
        $command = ChangeProfile::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());

        $handler = new ChangeProfileHandler($this->userRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }


    // ——————————————————————————————————————————————————————————————————————————
    // —— Master templates
    // ——————————————————————————————————————————————————————————————————————————

    public function formTemplate($jsonCommand): bool
    {
        $command = ForkTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);

        $handler = new ForkTemplateHandler(
            $this->masterTemplateRepository,
            $this->masterTemplateForkRepository,
            $this->userRepository
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
