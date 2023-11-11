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

use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;

class OrderTrackerNodeStatusHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected TrackerNodeStatusRepository $trackerNodeStatusRepository)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderTrackerNodeStatus $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->trackerNodeStatusRepository->getById($sequenceId);
            $model->setSequence($sequence);
            $this->trackerNodeStatusRepository->save($model);
            $sequence++;
        }
        return true;
    }
}
