<?php

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Wiki\SaveWikiNodeStatusWorkflow;

trait SaveWikiNodeStatusWorkflowTrait
{
    public function saveWikiNodeStatusWorkflow(\stdClass $jsonCommand): bool
    {
        $command = SaveWikiNodeStatusWorkflow::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $statusIds = [];
        foreach ($command->getWorkflow() as $workflowItem) {
            $statusIds[] = intval($workflowItem->getId());
        }
        $this->permission->CheckWikiNodeStatusIds($statusIds);

        $handler = new SaveWikiNodeStatusWorkflowHandler($this->wikiNodeStatusRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

}
