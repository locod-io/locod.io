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

namespace App\Lodocio\Application\Query\Wiki\ReadModel;

class WikiNodeStatusWorkflow implements \JsonSerializable
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

    public function addStatus(WikiNodeStatusWorkflowStatus $status): void
    {
        $this->workflow[] = $status;
    }

    public function addRelation(WikiNodeStatusWorkflowRelation $relation): void
    {
        $this->workflow[] = $relation;
    }

    public function getWorkflow(): array
    {
        return $this->workflow;
    }
}
