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

namespace App\Lodocio\Application\Command\Tracker\OrderTrackerNodeStatus;

trait OrderTrackerNodeStatusTrait
{
    public function orderTrackerNodeStatus(\stdClass $jsonCommand): bool
    {
        $command = OrderTrackerNodeStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeStatusIds($command->getSequence());

        $handler = new OrderTrackerNodeStatusHandler(
            $this->trackerNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
