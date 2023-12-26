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

namespace App\Locodio\Application\Security;

use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRole;
use Doctrine\ORM\EntityManagerInterface;

class BasePermissionService
{
    protected const NOT_ALLOWED_DATA = 'Action not allowed for this user.';
    protected const NOT_ALLOWED_ROLE = 'Action not allowed for this user.';

    protected array $rolesToCheck = [UserRole::ROLE_USER->value];

    // ————————————————————————————————————————————————————————————————————
    // Constructor
    // ————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ?User                  $user,
        protected EntityManagerInterface $entityManager,
        protected bool                   $isolationMode = false
    ) {
    }

    // ————————————————————————————————————————————————————————————————————
    // Checkers
    // ————————————————————————————————————————————————————————————————————

    public function CheckRole(array $roles): void
    {
        $this->rolesToCheck = $roles;
        if (!$this->isolationMode) {
            $isAllowed = false;
            if (!is_null($this->user)) {
                foreach ($roles as $role) {
                    if (in_array($role, $this->user->getRoles())) {
                        $isAllowed = true;
                        break;
                    }
                }
            }
            if (!$isAllowed) {
                throw new \Exception(self::NOT_ALLOWED_ROLE);
                exit();
            }
        }
    }

    public function CheckUserId(int $id): void
    {
        if (!$this->isolationMode) {
            if (is_null($this->user) || $this->user->getId() != $id) {
                throw new \Exception(self::NOT_ALLOWED_DATA);
                exit();
            }
        }
    }

    public function CheckOrganisationId(int $id): void
    {
        if (!$this->isolationMode) {
            $isAllowed = false;
            if (!is_null($this->user)) {
                foreach ($this->user->getOrganisationUsers() as $organisationUser) {
                    if ($organisationUser->getOrganisation()->getId() == $id) {
                        // -- extra check on the previously entered role for that organisation
                        foreach ($this->rolesToCheck as $roleToCheck) {
                            foreach ($organisationUser->getRoles() as $role) {
                                if ($roleToCheck === $role && $roleToCheck !== UserRole::ROLE_USER->value) {
                                    $isAllowed = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            if (!$isAllowed) {
                throw new \Exception(self::NOT_ALLOWED_DATA);
                exit();
            }
        }
    }

    public function CheckOtherUserId(int $id): void
    {
        if (!$this->isolationMode) {
            $userRepo = $this->entityManager->getRepository(User::class);
            $otherUser = $userRepo->getById($id);
            $isAllowed = false;
            // see if the logged-in user has a shared organisation with the other user
            foreach ($this->user->getOrganisationUsers() as $organisation) {
                foreach ($otherUser->getOrganisationUsers() as $otherOrganisation) {
                    if ($organisation->getOrganisation()->getId() === $otherOrganisation->getOrganisation()->getId()) {
                        $isAllowed = true;
                    }
                }
            }
            if (!$isAllowed) {
                throw new \Exception(self::NOT_ALLOWED_DATA);
                exit();
            }
        }
    }

    public function CheckProjectId(int $id): void
    {
        if (!$this->isolationMode) {
            $projectRepo = $this->entityManager->getRepository(Project::class);
            $project = $projectRepo->getById($id);
            $this->CheckOrganisationId($project->getOrganisation()->getId());
        }
    }

    public function CheckOrganisationIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckOrganisationId($id);
            }
        }
    }

    public function CheckProjectIds(array $ids): void
    {
        if (!$this->isolationMode) {
            foreach ($ids as $id) {
                $this->CheckProjectId($id);
            }
        }
    }
}
