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
use Doctrine\ORM\EntityManagerInterface;

class BasePermissionService
{
    protected const NOT_ALLOWED_DATA = 'Action not allowed for this user.';
    protected const NOT_ALLOWED_ROLE = 'Action not allowed for this user.';

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
                foreach ($this->user->getOrganisations() as $organisation) {
                    if ($organisation->getId() == $id) {
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
