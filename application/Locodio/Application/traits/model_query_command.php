<?php

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\Model\AddQuery\AddQuery;
use App\Locodio\Application\Command\Model\AddQuery\AddQueryHandler;
use App\Locodio\Application\Command\Model\ChangeQuery\ChangeQuery;
use App\Locodio\Application\Command\Model\ChangeQuery\ChangeQueryHandler;
use App\Locodio\Application\Command\Model\DeleteQuery\DeleteQuery;
use App\Locodio\Application\Command\Model\DeleteQuery\DeleteQueryHandler;
use App\Locodio\Application\Command\Model\OrderQuery\OrderQuery;
use App\Locodio\Application\Command\Model\OrderQuery\OrderQueryHandler;

trait model_query_command
{
    public function addQuery(\stdClass $jsonCommand): bool
    {
        $command = AddQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddQueryHandler($this->projectRepo, $this->domainModelRepo, $this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeQuery(\stdClass $jsonCommand): bool
    {
        $command = ChangeQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryId($command->getId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new ChangeQueryHandler($this->domainModelRepo, $this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderQuery(\stdClass $jsonCommand): bool
    {
        $command = OrderQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryIds($command->getSequence());

        $handler = new OrderQueryHandler($this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteQuery(\stdClass $jsonCommand): bool
    {
        $command = DeleteQuery::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckQueryId($command->getId());

        $handler = new DeleteQueryHandler($this->queryRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
