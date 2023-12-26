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

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\Model\AddEnum\AddEnum;
use App\Locodio\Application\Command\Model\AddEnum\AddEnumHandler;
use App\Locodio\Application\Command\Model\ChangeEnum\ChangeEnum;
use App\Locodio\Application\Command\Model\ChangeEnum\ChangeEnumHandler;
use App\Locodio\Application\Command\Model\DeleteEnum\DeleteEnum;
use App\Locodio\Application\Command\Model\DeleteEnum\DeleteEnumHandler;
use App\Locodio\Application\Command\Model\OrderEnum\OrderEnum;
use App\Locodio\Application\Command\Model\OrderEnum\OrderEnumHandler;

trait model_enum_command
{
    public function addEnum(\stdClass $jsonCommand): bool
    {
        $command = AddEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddEnumHandler(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->enumOptionRepo
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeEnum(\stdClass $jsonCommand): bool
    {
        $command = ChangeEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckEnumId($command->getId());
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new ChangeEnumHandler($this->domainModelRepo, $this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderEnum(\stdClass $jsonCommand): bool
    {
        $command = OrderEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckEnumIds($command->getSequence());

        $handler = new OrderEnumHandler($this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteEnum(\stdClass $jsonCommand): bool
    {
        $command = DeleteEnum::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckEnumId($command->getId());

        $handler = new DeleteEnumHandler($this->enumRepo, $this->enumOptionRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
