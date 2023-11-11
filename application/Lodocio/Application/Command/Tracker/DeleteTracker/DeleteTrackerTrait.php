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

namespace App\Lodocio\Application\Command\Tracker\DeleteTracker;

trait DeleteTrackerTrait
{
    /**
     * @throws \Exception
     */
    public function deleteTracker(\stdClass $jsonCommand): bool
    {
        $command = DeleteTracker::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerId($command->getId());

        $handler = new DeleteTrackerHandler(
            $this->trackerRepository,
            $this->trackerNodeRepository,
            $this->trackerNodeGroupRepository,
            $this->trackerNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
