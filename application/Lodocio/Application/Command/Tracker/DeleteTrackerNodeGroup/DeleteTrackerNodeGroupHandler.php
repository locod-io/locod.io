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

namespace App\Lodocio\Application\Command\Tracker\DeleteTrackerNodeGroup;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class DeleteTrackerNodeGroupHandler
{
    public function __construct(
        protected TrackerRepository          $trackerRepository,
        protected TrackerNodeRepository      $trackerNodeRepository,
        protected TrackerNodeGroupRepository $trackerNodeGroupRepository,
    ) {
    }

    public function go(DeleteTrackerNodeGroup $command): bool
    {
        $trackerGroup = $this->trackerNodeGroupRepository->getById($command->getId());
        $result = $this->trackerNodeGroupRepository->delete($trackerGroup);
        return true;
    }
}
