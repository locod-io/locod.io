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

namespace App\Lodocio\Application\Command\Tracker\SyncTrackerStructure;

trait SyncTrackerStructureTrait
{
    /**
     * @throws \Exception
     */
    public function syncTrackerStructure(\stdClass $jsonCommand): bool
    {
        $command = SyncTrackerStructure::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckTrackerId($command->getId());

        $handler = new SyncTrackerStructureHandler(
            $this->entityManager,
            $this->docProjectRepository,
            $this->trackerRepository,
            $this->trackerNodeStatusRepository,
            $this->trackerNodeRepository,
            $this->trackerNodeGroupRepository
        );
        $result = $handler->go($command);
        $this->entityManager->flush();

        return $result;
    }

}
