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
use App\Locodio\Domain\Model\User\UserRepository;

class AddOrganisationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository         $userRepo,
        protected OrganisationRepository $organisationRepo
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

        return true;
    }
}
