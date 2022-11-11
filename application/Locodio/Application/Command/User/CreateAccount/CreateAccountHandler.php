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
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRegistrationLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityNotFoundException;

class CreateAccountHandler
{
    public function __construct(
        protected UserRegistrationLinkRepository $userRegistrationLinkRepo,
        protected UserRepository                 $userRepo,
        protected OrganisationRepository         $organisationRepo,
        protected ProjectRepository              $projectRepo
    ) {
    }

    public function CreateAccount(CreateAccount $command): \stdClass
    {
        $result = new \stdClass();
        $result->message = '';

        // -- check validness of the link
        try {
            $registrationInfo = $this->userRegistrationLinkRepo->getByCode($command->getCode());
        } catch (EntityNotFoundException $exception) {
            $result->message = 'registration_link_not_valid';
            return $result;
        }
        if ($registrationInfo->isUsed()) {
            $result->message = 'registration_link_not_valid';
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
            $this->userRepo->nextIdentity(),
            $registrationInfo->getEmail(),
            $registrationInfo->getFirstname(),
            $registrationInfo->getLastname(),
            []
        );
        $user->setPassword($registrationInfo->getPassword());
        $organisation->addUser($user);

        $this->projectRepo->save($project);
        $this->organisationRepo->save($organisation);
        $this->userRepo->save($user);

        $registrationInfo->useLink();
        $this->userRegistrationLinkRepo->save($registrationInfo);

        $result->projectUuid = $project->getUuid()->toRfc4122();
        $result->message = 'account_created';

        return $result;
    }
}
