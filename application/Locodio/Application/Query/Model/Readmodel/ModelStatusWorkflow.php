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

namespace App\Locodio\Application\Query\Model\Readmodel;

class ModelStatusWorkflow implements \JsonSerializable
{
    public function __construct(
        protected array $workflow = []
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->workflow = $this->getWorkflow();
        return $json;
    }

    public function addStatus(ModelStatusWorkflowStatus $status): void
    {
        $this->workflow[] = $status;
    }

    public function addRelation(ModelStatusWorkflowRelation $relation): void
    {
        $this->workflow[] = $relation;
    }

    public function getWorkflow(): array
    {
        return $this->workflow;
    }
}
