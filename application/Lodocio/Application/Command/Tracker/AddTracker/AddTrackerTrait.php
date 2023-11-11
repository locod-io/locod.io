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

namespace App\Lodocio\Application\Command\Tracker\AddTracker;

trait AddTrackerTrait
{
    /**
     * @throws \Exception
     */
    public function addTracker(\stdClass $jsonCommand): bool
    {
        $command = AddTracker::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDocProjectId($command->getDocProjectId());

        $handler = new AddTrackerHandler(
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
