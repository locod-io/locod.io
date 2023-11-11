<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\SaveTrackerNodeStatusWorkflow;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;

class SaveTrackerNodeStatusWorkflowHandler
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

    public function go(SaveTrackerNodeStatusWorkflow $command): bool
    {
        foreach ($command->getWorkflow() as $workflowItem) {
            $modelStatus = $this->trackerNodeStatusRepository->getById(intval($workflowItem->getId()));
            $modelStatus->setWorkflow(
                $workflowItem->getPosition(),
                $workflowItem->getFlowIn(),
                $workflowItem->getFlowOut()
            );
            $this->trackerNodeStatusRepository->save($modelStatus);
        }

        return true;
    }
}
