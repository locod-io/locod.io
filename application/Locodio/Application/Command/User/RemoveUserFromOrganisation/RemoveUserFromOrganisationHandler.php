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

namespace App\Locodio\Application\Command\User\RemoveUserFromOrganisation;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Locodio\Infrastructure\Database\OrganisationUserRepository;

class RemoveUserFromOrganisationHandler
{
    public function __construct(
        protected UserRepository $userRepository,
        protected OrganisationRepository $organisationRepository,
        protected OrganisationUserRepository $organisationUserRepository,
    ) {
    }

    public function remove(RemoveUserFromOrganisation $command): bool
    {
        $organisation = $this->organisationRepository->getById($command->getOrganisationId());
        $user = $this->userRepository->getById($command->getUserId());

        $organisationUser = $this->organisationUserRepository->findByUserAndOrganisation($user, $organisation);
        $this->organisationUserRepository->delete($organisationUser);

        $user->removeOrganisation($organisation);
        $organisation->removeUser($user);
        $this->userRepository->save($user);
        $this->organisationRepository->save($organisation);

        return true;
    }

}
