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

use App\Locodio\Application\Query\Model\GetMasterTemplate;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRMCollection;
use App\Locodio\Application\Query\Organisation\GetOrganisation;
use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Application\Query\User\GetPasswordResetLink;
use App\Locodio\Application\Query\User\GetUser;
use App\Locodio\Application\Query\User\Readmodel\PasswordResetLinkRM;
use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class QueryBus
{
    protected BasePermissionService $permission;
    private const ADMIN = 4;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                    $security,
        protected EntityManagerInterface      $entityManager,
        protected bool                        $isolationMode,
        protected UserRepository              $userRepository,
        protected PasswordResetLinkRepository $passwordResetLinkRepository,
        protected OrganisationRepository      $organisationRepository,
        protected ProjectRepository           $projectRepository,
        protected MasterTemplateRepository    $masterTemplateRepository,
        protected DomainModelRepository       $domainModelRepository,
        protected DocumentorRepository        $documentorRepository,
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
    // —— User
    // ——————————————————————————————————————————————————————————————————————————

    public function checkUserByEmail(string $email): ?bool
    {
        $getUser = new GetUser($this->security, $this->userRepository);
        return $getUser->CheckByEmail($email);
    }

    public function getUserFromSession(): UserRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $getUser = new GetUser($this->security, $this->userRepository);
        if ($this->isolationMode) {
            return $getUser->ById(self::ADMIN);
        } else {
            return $getUser->FromSession();
        }
    }

    public function getOrganisationConfigForUser(): OrganisationRMCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $sessionUser = $this->getUserFromSession();
        $getOrganisation = new GetOrganisation($this->organisationRepository);
        return $getOrganisation->ByCollection($sessionUser->getOrganisations());
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Password Reset link
    // ——————————————————————————————————————————————————————————————————————————

    public function getPasswordResetLink($hash): PasswordResetLinkRM
    {
        $getLink = new GetPasswordResetLink($this->userRepository, $this->passwordResetLinkRepository);
        return $getLink->ByHash($hash);
    }

    public function getPasswordResetLinkByUuid($uuid): PasswordResetLinkRM
    {
        $getLink = new GetPasswordResetLink($this->userRepository, $this->passwordResetLinkRepository);
        return $getLink->ByUuid($uuid);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Master Templates
    // ——————————————————————————————————————————————————————————————————————————

    public function getMasterTemplatesForUser(): MasterTemplateRMCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $sessionUser = $this->getUserFromSession();
        $GetMasterTemplate = new GetMasterTemplate($this->masterTemplateRepository, $this->userRepository);
        return $GetMasterTemplate->ByUserId($sessionUser->getId());
    }

    // ——————————————————————————————————————————————————————————————————————————
    // —— Project
    // ——————————————————————————————————————————————————————————————————————————

    public function getProjectSummaryById(int $id): ProjectRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);
        $GetProject = new GetProject($this->projectRepository, $this->domainModelRepository, $this->documentorRepository);
        return $GetProject->SummaryById($id);
    }
}
