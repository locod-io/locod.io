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

use App\Linear\Application\Query\LinearConfig;
use App\Locodio\Application\Query\Model\GetMasterTemplate;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRMCollection;
use App\Locodio\Application\Query\Organisation\GetOrganisation;
use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Application\Query\User\GetPasswordResetLink;
use App\Locodio\Application\Query\User\Readmodel\PasswordResetLinkRM;
use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Application\traits\user_user_query;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserInvitationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class QueryBus
{
    use user_user_query;

    protected BasePermissionService $permission;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                     $security,
        protected EntityManagerInterface       $entityManager,
        protected bool                         $isolationMode,
        protected UserRepository               $userRepository,
        protected UserInvitationLinkRepository $userInvitationLinkRepository,
        protected PasswordResetLinkRepository  $passwordResetLinkRepository,
        protected OrganisationRepository       $organisationRepository,
        protected ProjectRepository            $projectRepository,
        protected MasterTemplateRepository     $masterTemplateRepository,
        protected DomainModelRepository        $domainModelRepository,
        protected DocumentorRepository         $documentorRepository,
        protected LinearConfig                 $linearConfig,
    ) {
        $this->permission = new BasePermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Output
    // ——————————————————————————————————————————————————————————————————————————

    public function helloFromQueryBus(): string
    {
        return 'hello from query bus';
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Password Reset link
    // ——————————————————————————————————————————————————————————————————————————

    public function getPasswordResetLink(string $hash): PasswordResetLinkRM
    {
        $getLink = new GetPasswordResetLink($this->userRepository, $this->passwordResetLinkRepository);
        return $getLink->ByHash($hash);
    }

    public function isPasswordResetLinkIsValid(string $signature, int $verificationCode): bool
    {
        $getLink = new GetPasswordResetLink($this->userRepository, $this->passwordResetLinkRepository);
        return $getLink->isPasswordResetLinkIsValid($signature, $verificationCode);
    }

    public function getPasswordResetLinkByUuid(string $uuid): PasswordResetLinkRM
    {
        $getLink = new GetPasswordResetLink($this->userRepository, $this->passwordResetLinkRepository);
        return $getLink->ByUuid($uuid);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Master Templates
    // ——————————————————————————————————————————————————————————————————————————

    public function getMasterTemplatesForUser(): MasterTemplateRMCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $sessionUser = $this->getUserFromSession();
        $GetMasterTemplate = new GetMasterTemplate($this->masterTemplateRepository, $this->userRepository);
        return $GetMasterTemplate->ByUserId($sessionUser->getId());
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Organisation
    // ——————————————————————————————————————————————————————————————————————————

    public function getOrganisationById(int $id): OrganisationRM
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckOrganisationId($id);
        $getOrganisation = new GetOrganisation(
            $this->organisationRepository,
            $this->projectRepository,
            $this->linearConfig,
        );
        return $getOrganisation->ById($id);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Project
    // ——————————————————————————————————————————————————————————————————————————

    public function getProjectSummaryById(int $id): ProjectRM
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckProjectId($id);
        $GetProject = new GetProject(
            $this->projectRepository,
            $this->domainModelRepository,
            $this->documentorRepository,
            $this->linearConfig
        );
        return $GetProject->SummaryById($id);
    }
}
