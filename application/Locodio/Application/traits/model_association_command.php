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

use App\Locodio\Application\Command\Model\AddAssociation\AddAssociation;
use App\Locodio\Application\Command\Model\AddAssociation\AddAssociationHandler;
use App\Locodio\Application\Command\Model\ChangeAssociation\ChangeAssociation;
use App\Locodio\Application\Command\Model\ChangeAssociation\ChangeAssociationHandler;
use App\Locodio\Application\Command\Model\DeleteAssociation\DeleteAssociation;
use App\Locodio\Application\Command\Model\DeleteAssociation\DeleteAssociationHandler;
use App\Locodio\Application\Command\Model\OrderAssociation\OrderAssociation;
use App\Locodio\Application\Command\Model\OrderAssociation\OrderAssociationHandler;

trait model_association_command
{
    public function addAssociation(\stdClass $jsonCommand): bool
    {
        $command = AddAssociation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddAssociationHandler($this->domainModelRepo, $this->associationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeAssociation(\stdClass $jsonCommand): bool
    {
        $command = ChangeAssociation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckAssociationId($command->getId());

        $handler = new ChangeAssociationHandler($this->domainModelRepo, $this->associationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderAssociation(\stdClass $jsonCommand): bool
    {
        $command = OrderAssociation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckAttributeIds($command->getSequence());

        $handler = new OrderAssociationHandler($this->associationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteAssociation(\stdClass $jsonCommand): bool
    {
        $command = DeleteAssociation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckAssociationId($command->getId());

        $handler = new DeleteAssociationHandler($this->associationRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
