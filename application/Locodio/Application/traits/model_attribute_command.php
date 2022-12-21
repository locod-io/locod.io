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

use App\Locodio\Application\Command\Model\AddAttribute\AddAttribute;
use App\Locodio\Application\Command\Model\AddAttribute\AddAttributeHandler;
use App\Locodio\Application\Command\Model\ChangeAttribute\ChangeAttribute;
use App\Locodio\Application\Command\Model\ChangeAttribute\ChangeAttributeHandler;
use App\Locodio\Application\Command\Model\DeleteAttribute\DeleteAttribute;
use App\Locodio\Application\Command\Model\DeleteAttribute\DeleteAttributeHandler;
use App\Locodio\Application\Command\Model\OrderAttribute\OrderAttribute;
use App\Locodio\Application\Command\Model\OrderAttribute\OrderAttributeHandler;

trait model_attribute_command
{
    public function addAttribute(\stdClass $jsonCommand): bool
    {
        $command = AddAttribute::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($command->getDomainModelId());

        $handler = new AddAttributeHandler($this->domainModelRepo, $this->attributeRepo, $this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeAttribute(\stdClass $jsonCommand): bool
    {
        $command = ChangeAttribute::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckAttributeId($command->getId());

        $handler = new ChangeAttributeHandler($this->attributeRepo, $this->enumRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderAttribute(\stdClass $jsonCommand): bool
    {
        $command = OrderAttribute::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckAttributeIds($command->getSequence());

        $handler = new OrderAttributeHandler($this->attributeRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteAttribute(\stdClass $jsonCommand): bool
    {
        $command = DeleteAttribute::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckAttributeId($command->getId());

        $handler = new DeleteAttributeHandler($this->attributeRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
