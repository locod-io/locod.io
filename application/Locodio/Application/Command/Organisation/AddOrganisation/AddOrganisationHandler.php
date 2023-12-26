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

namespace App\Locodio\Application\Command\Organisation\AddOrganisation;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\Organisation\OrganisationUserRepository;
use App\Locodio\Domain\Model\User\UserRepository;

class AddOrganisationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository             $userRepo,
        protected OrganisationRepository     $organisationRepo,
        protected OrganisationUserRepository $organisationUserRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddOrganisation $command): bool
    {
        $user = $this->userRepo->getById($command->getUserId());
        $organisation = Organisation::make(
            $this->organisationRepo->nextIdentity(),
            $command->getName(),
            strtoupper(substr($command->getName(), 0, 3))
        );
        $organisation->addUser($user);
        $this->organisationRepo->save($organisation);

        // create extra connection between user and organisation with admin role

        $organisationUser = $this->organisationUserRepo->findByUserAndOrganisation($user, $organisation);
        if (true === is_null($organisationUser)) {
            $organisationUser = OrganisationUser::make(
                $this->organisationUserRepo->nextIdentity(),
                $user,
                $organisation
            );
            $organisationUser->setRoles(['ROLE_ORGANISATION_VIEWER', 'ROLE_ORGANISATION_USER', 'ROLE_ORGANISATION_ADMIN']);
            $this->organisationUserRepo->save($organisationUser);
        }

        return true;
    }
}
