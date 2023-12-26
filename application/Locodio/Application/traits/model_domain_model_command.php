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

use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModel;
use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModelHandler;
use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModel;
use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModelHandler;
use App\Locodio\Application\Command\Model\DeleteDomainModel\DeleteDomainModel;
use App\Locodio\Application\Command\Model\DeleteDomainModel\DeleteDomainModelHandler;
use App\Locodio\Application\Command\Model\OrderDomainModel\OrderDomainModel;
use App\Locodio\Application\Command\Model\OrderDomainModel\OrderDomainModelHandler;

trait model_domain_model_command
{
    public function addDomainModel(\stdClass $jsonCommand): bool
    {
        $command = AddDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new AddDomainModelHandler($this->projectRepo, $this->domainModelRepo, $this->attributeRepo, $this->moduleRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeDomainModel(\stdClass $jsonCommand): bool
    {
        $command = ChangeDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckDomainModelId($command->getId());

        $handler = new ChangeDomainModelHandler($this->domainModelRepo, $this->moduleRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderDomainModel(\stdClass $jsonCommand): bool
    {
        $command = OrderDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckDomainModelIds($command->getSequence());

        $handler = new OrderDomainModelHandler($this->domainModelRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteDomainModel(\stdClass $jsonCommand): bool
    {
        $command = DeleteDomainModel::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckDomainModelId($command->getId());

        $handler = new DeleteDomainModelHandler($this->domainModelRepo, $this->attributeRepo, $this->associationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
