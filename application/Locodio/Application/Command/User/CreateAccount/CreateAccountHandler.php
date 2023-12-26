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

namespace App\Locodio\Application\Command\User\CreateAccount;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\OrganisationUserRepository;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRegistrationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Locodio\Domain\Model\User\UserRole;
use Doctrine\ORM\EntityNotFoundException;

class CreateAccountHandler
{
    public function __construct(
        protected UserRegistrationLinkRepository $userRegistrationLinkRepo,
        protected UserRepository                 $userRepo,
        protected OrganisationRepository         $organisationRepo,
        protected ProjectRepository              $projectRepo,
        protected OrganisationUserRepository     $organisationUserRepository,
    ) {
    }

    public function CreateAccount(CreateAccount $command): \stdClass
    {
        $result = new \stdClass();
        $result->message = '';

        // -- check validness of the link
        try {
            $registrationInfo = $this->userRegistrationLinkRepo->getByCode($command->getSignature());
        } catch (EntityNotFoundException $exception) {
            $result->message = 'registration_link_not_valid';
            return $result;
        }
        if ($registrationInfo->isUsed()) {
            $result->message = 'registration_link_not_valid';
            return $result;
        }

        // -- check if the code is valid
        $signatureToCheck = hash('sha256', strtolower($registrationInfo->getEmail()) . $command->getCode() . $_SERVER['APP_SECRET']);
        if ($signatureToCheck !== $command->getSignature()) {
            $result->message = 'verification_code_not_valid';
            return $result;
        }

        // -- check validness of the user
        try {
            $user = $this->userRepo->getByEmail($registrationInfo->getEmail());
            $result->message = 'registration_email_already_registered';
            return $result;
        } catch (EntityNotFoundException $exception) {
        }

        // -- convert the registration info to an user account
        $organisation = Organisation::make(
            $this->organisationRepo->nextIdentity(),
            $registrationInfo->getOrganisation(),
            strtoupper(substr($registrationInfo->getOrganisation(), 0, 3))
        );
        $this->organisationRepo->save($organisation);
        $project = Project::make(
            $this->projectRepo->nextIdentity(),
            'My first project',
            'MFP',
            $organisation
        );
        $this->projectRepo->save($project);

        $user = User::make(
            uuid: $this->userRepo->nextIdentity(),
            email: $registrationInfo->getEmail(),
            firstname: $registrationInfo->getFirstname(),
            lastname: $registrationInfo->getLastname(),
            roles: [UserRole::ROLE_ORGANISATION_ADMIN->value, UserRole::ROLE_ORGANISATION_USER->value, UserRole::ROLE_ORGANISATION_VIEWER->value]
        );

        $user->setPassword($registrationInfo->getPassword());
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
                UserRole::ROLE_ORGANISATION_VIEWER->value
            ]);
            $this->organisationUserRepository->save($organisationUser);
        }

        // -- invalidate the link

        $registrationInfo->useLink();
        $this->userRegistrationLinkRepo->save($registrationInfo);

        $result->projectUuid = $project->getUuid()->toRfc4122();
        $result->message = 'account_created';

        return $result;
    }
}
