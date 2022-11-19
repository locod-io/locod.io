<?php

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOption;
use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOptionHandler;
use App\Locodio\Application\Command\Model\ChangeEnumOption\ChangeEnumOption;
use App\Locodio\Application\Command\Model\ChangeEnumOption\ChangeEnumOptionHandler;
use App\Locodio\Application\Command\Model\DeleteEnumOption\DeleteEnumOption;
use App\Locodio\Application\Command\Model\DeleteEnumOption\DeleteEnumOptionHandler;
use App\Locodio\Application\Command\Model\OrderEnumOption\OrderEnumOption;
use App\Locodio\Application\Command\Model\OrderEnumOption\OrderEnumOptionHandler;

trait model_enum_option_command
{
    public function addEnumOption(\stdClass $jsonCommand): bool
    {
        $command = AddEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumId($command->getEnumId());

        $handler = new AddEnumOptionHandler($this->enumRepo, $this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeEnumOption(\stdClass $jsonCommand): bool
    {
        $command = ChangeEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumOptionId($command->getId());

        $handler = new ChangeEnumOptionHandler($this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderEnumOption(\stdClass $jsonCommand): bool
    {
        $command = OrderEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumOptionIds($command->getSequence());

        $handler = new OrderEnumOptionHandler($this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteEnumOption(\stdClass $jsonCommand): bool
    {
        $command = DeleteEnumOption::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumOptionId($command->getId());

        $handler = new DeleteEnumOptionHandler($this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
