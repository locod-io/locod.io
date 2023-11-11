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

use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class AddTrackerNodeStatusHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected TrackerRepository           $trackerRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(AddTrackerNodeStatus $command): bool
    {
        $tracker = $this->trackerRepository->getById($command->getTrackerId());

        if ($command->isFinal() || $command->isStart()) {
            $trackerStatus = $this->trackerNodeStatusRepository->getByTracker($tracker);
            foreach ($trackerStatus as $status) {
                if ($command->isFinal()) {
                    $status->deFinalize();
                    $this->trackerNodeStatusRepository->save($status);
                }
                if ($command->isStart()) {
                    $status->deStarterize();
                    $this->trackerNodeStatusRepository->save($status);
                }
            }
        }

        $model = TrackerNodeStatus::make(
            $tracker,
            $this->trackerNodeStatusRepository->nextIdentity(),
            $command->getName(),
            $command->getColor(),
            $command->isStart(),
            $command->isFinal(),
        );

        $model->setSequence($this->trackerNodeStatusRepository->getMaxSequence($tracker));
        $model->setArtefactId($this->trackerNodeStatusRepository->getNextArtefactId($tracker));
        $this->trackerNodeStatusRepository->save($model);

        return true;
    }
}
