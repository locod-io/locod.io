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

use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class OrderTrackerHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected TrackerRepository $trackerRepository)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderTracker $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->trackerRepository->getById($sequenceId);
            $model->setSequence($sequence);
            $this->trackerRepository->save($model);
            $sequence++;
        }
        return true;
    }
}
