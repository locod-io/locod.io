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

namespace App\Lodocio\Application\Command\Tracker\ChangeTrackerNodeStatus;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;

class ChangeTrackerNodeStatusHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeTrackerNodeStatus $command): bool
    {
        $model = $this->trackerNodeStatusRepository->getById($command->getId());

        if ($command->isFinal() || $command->isStart()) {
            $modelStatus = $this->trackerNodeStatusRepository->getByTracker($model->getTracker());
            foreach ($modelStatus as $status) {
                if ($status->getId() !== $model->getId()) {
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
        }

        $model->change(
            $command->getName(),
            $command->getColor(),
            $command->isStart(),
            $command->isFinal(),
        );
        $this->trackerNodeStatusRepository->save($model);
        return true;
    }
}
