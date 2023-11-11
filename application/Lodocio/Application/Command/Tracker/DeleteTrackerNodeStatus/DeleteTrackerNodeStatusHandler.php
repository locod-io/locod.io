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

namespace App\Lodocio\Application\Command\Tracker\DeleteTrackerNodeStatus;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;

class DeleteTrackerNodeStatusHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected TrackerNodeRepository       $trackerNodeRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteTrackerNodeStatus $command): bool
    {
        $status = $this->trackerNodeStatusRepository->getById($command->getId());
        $usages = $this->trackerNodeRepository->countByStatus($status->getId());
        if ($usages > 0) {
            return false;
        }
        $this->trackerNodeStatusRepository->delete($status);

        return true;
    }
}
