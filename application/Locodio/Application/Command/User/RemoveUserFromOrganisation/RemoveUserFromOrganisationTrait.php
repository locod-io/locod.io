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

use App\Locodio\Domain\Model\User\UserRole;

trait RemoveUserFromOrganisationTrait
{
    /**
     * @throws \Exception
     */
    public function removeUserFromOrganisation(\stdClass $jsonCommand): bool
    {
        $command = RemoveUserFromOrganisation::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_ADMIN->value]);
        $this->permission->CheckOrganisationId($command->getOrganisationId());
        $this->permission->CheckOtherUserId($command->getUserId());

        $handler = new RemoveUserFromOrganisationHandler(
            $this->userRepository,
            $this->organisationRepository,
            $this->organisationUserRepository,
        );
        $result = $handler->remove($command);
        $this->entityManager->flush();

        return $result;
    }
}
