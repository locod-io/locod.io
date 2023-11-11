<?php

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\SaveTrackerNodeStatusWorkflow;

trait SaveTrackerNodeStatusWorkflowTrait
{
    public function saveTrackerNodeStatusWorkflow(\stdClass $jsonCommand): bool
    {
        $command = SaveTrackerNodeStatusWorkflow::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $statusIds = [];
        foreach ($command->getWorkflow() as $workflowItem) {
            $statusIds[] = intval($workflowItem->getId());
        }
        $this->permission->CheckTrackerNodeStatusIds($statusIds);

        $handler = new SaveTrackerNodeStatusWorkflowHandler($this->trackerNodeStatusRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

}
