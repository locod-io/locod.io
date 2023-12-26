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

namespace App\Locodio\Application\traits;

use App\Linear\Application\Query\Readmodel\RoadmapReadModelCollection;
use App\Locodio\Application\Query\Organisation\GetOrganisation;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Application\Query\User\GetUser;
use App\Locodio\Application\Query\User\Readmodel\UserInvitationLinkRMCollection;
use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Application\Query\User\Readmodel\UserRMCollection;
use App\Locodio\Domain\Model\User\UserRole;

trait user_user_query
{
    private const ADMIN = 2;
    private const USER = 3;

    public function checkUserByEmail(string $email): ?bool
    {
        $getUser = new GetUser(
            $this->security,
            $this->userRepository,
            $this->organisationRepository,
            $this->userInvitationLinkRepository,
        );
        return $getUser->CheckByEmail($email);
    }

    public function getUserFromSession(): UserRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $getUser = new GetUser(
            $this->security,
            $this->userRepository,
            $this->organisationRepository,
            $this->userInvitationLinkRepository,
        );
        if ($this->isolationMode) {
            return $getUser->ById(self::ADMIN);
        } else {
            return $getUser->FromSession();
        }
    }

    public function getOrganisationConfigForUser(): OrganisationRMCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $sessionUser = $this->getUserFromSession();
        $getOrganisation = new GetOrganisation($this->organisationRepository, $this->projectRepository, $this->linearConfig);
        return $getOrganisation->ByCollection($sessionUser->getOrganisations());
    }

    public function getRoadmapsForUser(): RoadmapReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $sessionUser = $this->getUserFromSession();
        $getOrganisation = new GetOrganisation($this->organisationRepository, $this->projectRepository, $this->linearConfig);
        return $getOrganisation->RoadmapsByCollection($sessionUser->getOrganisations());
    }

    // ———————————————————————————————————————————————————————————————
    // Organisation Admin functions
    // ———————————————————————————————————————————————————————————————

    public function getUsersByOrganisation(int $organisationId): UserRMCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_ADMIN->value]);
        $this->permission->CheckOrganisationId($organisationId);
        $getUser = new GetUser(
            $this->security,
            $this->userRepository,
            $this->organisationRepository,
            $this->userInvitationLinkRepository,
        );
        return $getUser->ByOrganisation($organisationId);
    }

    public function getActiveInvitationsByOrganisation(int $organisationId): UserInvitationLinkRMCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_ADMIN->value]);
        $this->permission->CheckOrganisationId($organisationId);
        $getUser = new GetUser(
            $this->security,
            $this->userRepository,
            $this->organisationRepository,
            $this->userInvitationLinkRepository,
        );
        return $getUser->ActiveInvitationsByOrganisation($organisationId);
    }

    /**
     * @throws \Exception
     */
    public function getUserDetail(int $userId, int $organisationId): UserRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_ADMIN->value]);
        $this->permission->CheckOrganisationId($organisationId);
        $this->permission->CheckOtherUserId($userId);

        $getUser = new GetUser(
            $this->security,
            $this->userRepository,
            $this->organisationRepository,
            $this->userInvitationLinkRepository,
        );
        return $getUser->ById($userId, $organisationId);
    }
}
