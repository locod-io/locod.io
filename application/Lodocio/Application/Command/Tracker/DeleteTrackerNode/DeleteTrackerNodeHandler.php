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

namespace App\Lodocio\Application\Command\Tracker\DeleteTrackerNode;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;

class DeleteTrackerNodeHandler
{
    public function __construct(
        protected TrackerNodeRepository $trackerNodeRepository
    ) {
    }

    public function go(DeleteTrackerNode $command): bool
    {
        $trackerNode = $this->trackerNodeRepository->getById($command->getId());
        $this->trackerNodeRepository->delete($trackerNode);

        return true;
    }

}
