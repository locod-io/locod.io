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

namespace App\Lodocio\Application\Command\Wiki\SaveWikiNodeStatusWorkflow;

use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;

class SaveWikiNodeStatusWorkflowHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected WikiNodeStatusRepository $wikiNodeStatusRepository)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(SaveWikiNodeStatusWorkflow $command): bool
    {
        foreach ($command->getWorkflow() as $workflowItem) {
            $modelStatus = $this->wikiNodeStatusRepository->getById(intval($workflowItem->getId()));
            $modelStatus->setWorkflow(
                $workflowItem->getPosition(),
                $workflowItem->getFlowIn(),
                $workflowItem->getFlowOut()
            );
            $this->wikiNodeStatusRepository->save($modelStatus);
        }

        return true;
    }
}
