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

namespace App\Locodio\Application\Command\Model\SaveModelStatusWorkflow;

use App\Locodio\Domain\Model\Model\ModelStatusRepository;

class SaveModelStatusWorkflowHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected ModelStatusRepository $modelStatusRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(SaveModelStatusWorkflow $command): bool
    {
        foreach ($command->getWorkflow() as $workflowItem) {
            $modelStatus = $this->modelStatusRepo->getById(intval($workflowItem->getId()));
            $modelStatus->setWorkflow(
                $workflowItem->getPosition(),
                $workflowItem->getFlowIn(),
                $workflowItem->getFlowOut()
            );
            $this->modelStatusRepo->save($modelStatus);
        }

        return true;
    }
}
