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

namespace App\Locodio\Application\Query\User;

use App\Locodio\Application\Query\User\Readmodel\UserInvitationLinkRM;
use App\Locodio\Application\Query\User\Readmodel\UserInvitationLinkRMCollection;
use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Application\Query\User\Readmodel\UserRMCollection;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserInvitationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Assert\Assertion;
use Assert\InvalidArgumentException;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;

class GetUser
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected Security                     $security,
        protected UserRepository               $userRepo,
        protected OrganisationRepository       $organisationRepository,
        protected UserInvitationLinkRepository $userInvitationLinkRepository,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Read information
    // ———————————————————————————————————————————————————————————————

    public function FromSession(): UserRM
    {
        /** @var User $user */
        $user = $this->security->getUser();
        return UserRM::hydrateFromModel($user, true);
    }

    public function ByOrganisation(int $organisationId): UserRMCollection
    {
        $organisation = $this->organisationRepository->getById($organisationId);
        $collection = new UserRMCollection();
        $users = $this->userRepo->getByOrganisation($organisation);
        foreach ($users as $user) {
            $userRM = UserRM::hydrateFromModel($user, true);
            $userRM->stripOrganisations($organisationId);
            $collection->addItem($userRM);
        }
        return $collection;
    }

    public function ActiveInvitationsByOrganisation(int $organisationId): UserInvitationLinkRMCollection
    {
        $organisation = $this->organisationRepository->getById($organisationId);
        $collection = new UserInvitationLinkRMCollection();
        $invitations = $this->userInvitationLinkRepository->getActiveByOrganisation($organisation);
        foreach ($invitations as $invitation) {
            $collection->addItem(UserInvitationLinkRM::hydrateFromModel($invitation));
        }
        return $collection;
    }

    public function ById(int $id, int $organisationId = 0): UserRM
    {
        $user = UserRM::hydrateFromModel($this->userRepo->getById($id), true);
        if ($organisationId !== 0) {
            $user->stripOrganisations($organisationId);
        }
        return $user;
    }

    public function CheckByEmail(string $email): ?bool
    {
        try {
            Assertion::email($email);
        } catch (InvalidArgumentException $e) {
            return null;
        }
        try {
            $user = $this->userRepo->getByEmail(trim($email));
            return true;
        } catch (EntityNotFoundException $e) {
            return false;
        }
    }
}
