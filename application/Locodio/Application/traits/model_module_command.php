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

use App\Locodio\Application\Command\Model\AddModule\AddModule;
use App\Locodio\Application\Command\Model\AddModule\AddModuleHandler;
use App\Locodio\Application\Command\Model\ChangeModule\ChangeModule;
use App\Locodio\Application\Command\Model\ChangeModule\ChangeModuleHandler;
use App\Locodio\Application\Command\Model\DeleteModule\DeleteModule;
use App\Locodio\Application\Command\Model\DeleteModule\DeleteModuleHandler;
use App\Locodio\Application\Command\Model\OrderModule\OrderModule;
use App\Locodio\Application\Command\Model\OrderModule\OrderModuleHandler;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

trait model_module_command
{
    public function addModule(\stdClass $jsonCommand): bool
    {
        $command = AddModule::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new AddModuleHandler($this->projectRepo, $this->moduleRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeModule(\stdClass $jsonCommand): bool
    {
        $command = ChangeModule::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModuleId($command->getId());

        $handler = new ChangeModuleHandler($this->moduleRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderModule(\stdClass $jsonCommand): bool
    {
        $command = OrderModule::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModuleIds($command->getSequence());

        $handler = new OrderModuleHandler($this->moduleRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deleteModule(\stdClass $jsonCommand): bool
    {
        $command = DeleteModule::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModuleId($command->getId());

        $handler = new DeleteModuleHandler($this->moduleRepo, $this->domainModelRepo, $this->documentorRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
