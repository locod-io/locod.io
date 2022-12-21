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

use App\Locodio\Application\Command\Model\AddModelStatus\AddModelStatus;
use App\Locodio\Application\Command\Model\AddModelStatus\AddModelStatusHandler;
use App\Locodio\Application\Command\Model\ChangeModelStatus\ChangeModelStatus;
use App\Locodio\Application\Command\Model\ChangeModelStatus\ChangeModelStatusHandler;
use App\Locodio\Application\Command\Model\DeleteModelStatus\DeleteModelStatus;
use App\Locodio\Application\Command\Model\DeleteModelStatus\DeleteModelStatusHandler;
use App\Locodio\Application\Command\Model\OrderModelStatus\OrderModelStatus;
use App\Locodio\Application\Command\Model\OrderModelStatus\OrderModelStatusHandler;
use App\Locodio\Application\Command\Model\SaveModelStatusWorkflow\SaveModelStatusWorkflow;
use App\Locodio\Application\Command\Model\SaveModelStatusWorkflow\SaveModelStatusWorkflowHandler;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

trait model_status_command
{
    public function addModelStatus(\stdClass $jsonCommand): bool
    {
        $command = AddModelStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new AddModelStatusHandler($this->projectRepo, $this->modelStatusRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeModelStatus(\stdClass $jsonCommand): bool
    {
        $command = ChangeModelStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModelStatusId($command->getId());

        $handler = new ChangeModelStatusHandler($this->modelStatusRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderModelStatus(\stdClass $jsonCommand): bool
    {
        $command = OrderModelStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModelStatusIds($command->getSequence());

        $handler = new OrderModelStatusHandler($this->modelStatusRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function saveModelStatusWorkflow(\stdClass $jsonCommand): bool
    {
        $command = SaveModelStatusWorkflow::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $statusIds = [];
        foreach ($command->getWorkflow() as $workflowItem) {
            $statusIds[] = intval($workflowItem->getId());
        }
        $this->permission->CheckModelStatusIds($statusIds);

        $handler = new SaveModelStatusWorkflowHandler($this->modelStatusRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deleteModelStatus(\stdClass $jsonCommand): bool
    {
        $command = DeleteModelStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModelStatusId($command->getId());

        $handler = new DeleteModelStatusHandler($this->modelStatusRepo, $this->documentorRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
