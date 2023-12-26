<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\ChangeTracker;

trait ChangeTrackerTrait
{
    public function changeTracker(\stdClass $jsonCommand): bool
    {
        $command = ChangeTracker::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckTrackerId($command->getId());

        $handler = new ChangeTrackerHandler(
            $this->trackerRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
