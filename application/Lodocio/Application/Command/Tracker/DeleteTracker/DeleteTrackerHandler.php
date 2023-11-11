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

use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeRepository;

class DeleteTrackerHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected TrackerRepository           $trackerRepository,
        protected TrackerNodeRepository       $trackerNodeRepository,
        protected TrackerNodeGroupRepository  $trackerNodeGroupRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository,
    ) {
    }

    public function go(DeleteTracker $command): bool
    {
        $tracker = $this->trackerRepository->getById($command->getId());

        // if (count($tracker->getTrackerNodes()) > 0) {
        //     return false;
        // }

        // -- delete all the related nodes
        foreach ($tracker->getTrackerNodes() as $node) {
            $this->trackerNodeRepository->delete($node);
        }

        // -- delete all the selected groups
        foreach ($tracker->getTrackerGroups() as $group) {
            $this->trackerNodeGroupRepository->delete($group);
        }

        // -- delete all the related status
        foreach ($tracker->getTrackerNodeStatus() as $status) {
            $this->trackerNodeStatusRepository->delete($status);
        }

        // -- delete the tracker
        $this->trackerRepository->delete($tracker);

        return true;
    }

}
