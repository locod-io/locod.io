<?php

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\Model\AddCommand\AddCommand;
use App\Locodio\Application\Command\Model\AddCommand\AddCommandHandler;
use App\Locodio\Application\Command\Model\ChangeCommand\ChangeCommand;
use App\Locodio\Application\Command\Model\ChangeCommand\ChangeCommandHandler;
use App\Locodio\Application\Command\Model\DeleteCommand\DeleteCommand;
use App\Locodio\Application\Command\Model\DeleteCommand\DeleteCommandHandler;
use App\Locodio\Application\Command\Model\OrderCommand\OrderCommand;
use App\Locodio\Application\Command\Model\OrderCommand\OrderCommandHandler;

trait model_command_command
{
    public function addCommand(\stdClass $jsonCommand): bool
    {
        $command = AddCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddCommandHandler($this->projectRepo, $this->domainModelRepo, $this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeCommand(\stdClass $jsonCommand): bool
    {
        $command = ChangeCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandId($command->getId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new ChangeCommandHandler($this->domainModelRepo, $this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderCommand(\stdClass $jsonCommand): bool
    {
        $command = OrderCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandIds($command->getSequence());

        $handler = new OrderCommandHandler($this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteCommand(\stdClass $jsonCommand): bool
    {
        $command = DeleteCommand::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckCommandId($command->getId());

        $handler = new DeleteCommandHandler($this->commandRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
