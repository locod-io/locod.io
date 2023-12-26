<?php

declare(strict_types=1);

namespace App\Locodio\Application\Command\User\ChangeOrganisationUserRoles;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationUserRepository;
use App\Locodio\Domain\Model\User\UserRepository;

class ChangeOrganisationUserRolesHandler
{
    public function __construct(
        protected UserRepository             $userRepository,
        protected OrganisationRepository     $organisationRepository,
        protected OrganisationUserRepository $organisationUserRepository
    ) {
    }

    public function changeRoles(ChangeOrganisationUserRoles $command): bool
    {
        $user = $this->userRepository->getById($command->getUserId());
        $organisation = $this->organisationRepository->getById($command->getOrganisationId());
        $organisationUser = $this->organisationUserRepository->findByUserAndOrganisation($user, $organisation);

        if (false === is_null($organisationUser)) {

            $newUserRoles = $command->getRoles();

            // -- set the new roles for the organisation user
            $organisationUser->setRoles($command->getRoles());
            $this->organisationUserRepository->save($organisationUser);

            // get the roles of other organisations users and collect them all and make them unique
            $allOrganisationRoles = $this->organisationUserRepository->getByUser($user);
            foreach ($allOrganisationRoles as $organisationUser) {
                foreach ($organisationUser->getRoles() as $role) {
                    $newUserRoles[] = $role;
                }
            }
            $uniqueUserRoles = array_unique($newUserRoles);
            $user->setRoles($uniqueUserRoles);
            $this->userRepository->save($user);

            return true;
        } else {
            return false;
        }
    }

}
