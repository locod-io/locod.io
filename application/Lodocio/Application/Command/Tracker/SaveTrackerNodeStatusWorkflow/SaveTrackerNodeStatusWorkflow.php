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

class SaveTrackerNodeStatusWorkflow
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected array $workflow
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        $workflow = [];
        foreach ($json->workflow as $workflowItem) {
            $item = WorkflowItem::hydrateFromJson($workflowItem);
            $workflow[] = $item;
        }
        return new self($workflow);
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    /**
     * @return WorkflowItem[]
     */
    public function getWorkflow(): array
    {
        return $this->workflow;
    }
}
