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

namespace App\Lodocio\Application\Command\Tracker\AddTrackerNodeStatus;

trait AddTrackerNodeStatusTrait
{
    /**
     * @throws \Exception
     */
    public function addTrackerNodeStatus(\stdClass $jsonCommand): bool
    {
        $command = AddTrackerNodeStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerId($command->getTrackerId());

        $handler = new AddTrackerNodeStatusHandler(
            $this->trackerRepository,
            $this->trackerNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
