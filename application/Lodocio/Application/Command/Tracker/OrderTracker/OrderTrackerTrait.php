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

namespace App\Lodocio\Application\Command\Tracker\OrderTracker;

trait OrderTrackerTrait
{
    public function orderTrackers(\stdClass $jsonCommand): bool
    {
        $command = OrderTracker::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckTrackersId($command->getSequence());

        $handler = new OrderTrackerHandler(
            $this->trackerRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
