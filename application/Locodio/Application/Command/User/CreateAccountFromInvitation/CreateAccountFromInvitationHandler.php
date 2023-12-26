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

namespace App\Locodio\Application\Command\User\CreateAccountFromInvitation;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\OrganisationUserRepository;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserInvitationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Locodio\Domain\Model\User\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAccountFromInvitationHandler
{
    public function __construct(
        protected UserInvitationLinkRepository $invitationLinkRepository,
        protected UserRepository               $userRepository,
        protected OrganisationRepository       $organisationRepository,
        protected OrganisationUserRepository   $organisationUserRepository,
        protected UserPasswordHasherInterface  $passwordEncoder
    ) {
    }

    public function create(CreateAccountFromInvitation $command): bool
    {
        if (false === $command->isPasswordValid()) {
            return false;
        }
        $invitationLink = $this->invitationLinkRepository->getByCode($command->getSignature());
        if ($invitationLink->isUsed()) {
            return false;
        }
        $signatureToCheck = hash('sha256', $invitationLink->getEmail() . $command->getCode() . $_SERVER['APP_SECRET']);
        if ($signatureToCheck !== $command->getSignature()) {
            return false;
        }

        $organisation = $invitationLink->getOrganisation();
        $user = User::make(
            uuid: $this->userRepository->nextIdentity(),
            email: $invitationLink->getEmail(),
            firstname: $command->getFirstName(),
            lastname: $command->getLastName(),
            roles: [UserRole::ROLE_ORGANISATION_USER->value],
        );
        $this->userRepository->save($user);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $command->getPassword1()));
        $user->addOrganisation($organisation);
        $organisation->addUser($user);
        $this->userRepository->save($user);
        $this->organisationRepository->save($organisation);

        $organisationUser = $this->organisationUserRepository->findByUserAndOrganisation(user:$user, organisation: $organisation);
        if(true === is_null($organisationUser)) {
            $organisationUser = OrganisationUser::make(
                uuid: $this->organisationUserRepository->nextIdentity(),
                user: $user,
                organisation: $organisation,
            );
            $organisationUser->setRoles([UserRole::ROLE_ORGANISATION_VIEWER->value,UserRole::ROLE_ORGANISATION_USER->value]);
            $this->organisationUserRepository->save($organisationUser);
        }

        $invitationLink->invalidate();
        $this->invitationLinkRepository->save($invitationLink);

        return true;
    }

}
