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
use App\Locodio\Domain\Model\Model\MasterTemplateForkRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRegistrationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class CommandBus
{
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
        protected MasterTemplateForkRepository   $masterTemplateForkRepository
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

    // ——————————————————————————————————————————————————————————————————————————
    // —— Organisation
    // ——————————————————————————————————————————————————————————————————————————

    public function addOrganisation(\stdClass $jsonCommand): bool
    {
        $command = AddOrganisation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());

        $handler = new AddOrganisationHandler($this->userRepository, $this->organisationRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeOrganisation(\stdClass $jsonCommand): bool
    {
        $command = ChangeOrganisation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($command->getId());

        $handler = new ChangeOrganisationHandler($this->organisationRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderOrganisations(\stdClass $jsonCommand): bool
    {
        $command = OrderOrganisation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationIds($command->getSequence());

        $handler = new OrderOrganisationHandler($this->organisationRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Project
    // ——————————————————————————————————————————————————————————————————————————

    public function addProject(\stdClass $jsonCommand): bool
    {
        $command = AddProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($command->getOrganisationId());

        $handler = new AddProjectHandler($this->organisationRepository, $this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeProject(\stdClass $jsonCommand): bool
    {
        $command = ChangeProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getId());

        $handler = new ChangeProjectHandler($this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderProjects(\stdClass $jsonCommand): bool
    {
        $command = OrderProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectIds($command->getSequence());

        $handler = new OrderProjectHandler($this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
