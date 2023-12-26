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

namespace App\Locodio\Application\Command\User\CreateAccountFromGitHub;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\OrganisationUserRepository;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Locodio\Domain\Model\User\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class CreateAccountFromGitHubHandler
{
    public function __construct(
        protected UserRepository              $userRepo,
        protected OrganisationRepository      $organisationRepo,
        protected ProjectRepository           $projectRepo,
        protected OrganisationUserRepository  $organisationUserRepository,
        protected UserPasswordHasherInterface $passwordEncoder,
    ) {
    }

    public function register(CreateAccountFromGitHub $command): bool
    {
        $user = $this->userRepo->findByEmail($command->getEmail());
        if (false === is_null($user)) {
            return false;
        }

        // -- make an organisation
        $organisation = Organisation::make(
            $this->organisationRepo->nextIdentity(),
            $command->getOrganisation(),
            strtoupper(substr(str_replace(" ", "", $command->getOrganisation()), 0, 3)),
        );
        $this->organisationRepo->save($organisation);

        // -- make a project
        $project = Project::make(
            $this->projectRepo->nextIdentity(),
            'My first project',
            'MFP',
            $organisation
        );
        $this->projectRepo->save($project);

        // -- make a user
        $user = User::make(
            uuid: $this->userRepo->nextIdentity(),
            email: $command->getEmail(),
            firstname: $command->getFirstName(),
            lastname: $command->getLastName(),
            roles: [
                UserRole::ROLE_ORGANISATION_ADMIN->value,
                UserRole::ROLE_ORGANISATION_USER->value,
                UserRole::ROLE_ORGANISATION_VIEWER->value,
            ]
        );
        $user->setPassword($this->passwordEncoder->hashPassword($user, Uuid::v4()->toRfc4122()));
        $user->setProvider('github');
        $user->addOrganisation($organisation);
        $this->userRepo->save($user);

        $organisation->addUser($user);
        $this->projectRepo->save($project);
        $this->organisationRepo->save($organisation);
        $this->userRepo->save($user);

        // -- also register this user in the organisation users permissions table
        $organisationUser = $this->organisationUserRepository->findByUserAndOrganisation($user, $organisation);
        if (true === is_null($organisationUser)) {
            $organisationUser = OrganisationUser::make(
                uuid: $this->organisationUserRepository->nextIdentity(),
                user: $user,
                organisation: $organisation
            );
            $organisationUser->setRoles([
                UserRole::ROLE_ORGANISATION_ADMIN->value,
                UserRole::ROLE_ORGANISATION_USER->value,
                UserRole::ROLE_ORGANISATION_VIEWER->value,
            ]);
            $this->organisationUserRepository->save($organisationUser);
        }

        return true;
    }

}
