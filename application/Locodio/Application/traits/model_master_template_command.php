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

use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplate;
use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeMasterTemplate\ChangeMasterTemplate;
use App\Locodio\Application\Command\Model\ChangeMasterTemplate\ChangeMasterTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeMasterTemplateContent\ChangeMasterTemplateContent;
use App\Locodio\Application\Command\Model\ChangeMasterTemplateContent\ChangeMasterTemplateContentHandler;
use App\Locodio\Application\Command\Model\DeleteMasterTemplate\DeleteMasterTemplate;
use App\Locodio\Application\Command\Model\DeleteMasterTemplate\DeleteMasterTemplateHandler;
use App\Locodio\Application\Command\Model\OrderMasterTemplate\OrderMasterTemplate;
use App\Locodio\Application\Command\Model\OrderMasterTemplate\OrderMasterTemplateHandler;

trait model_master_template_command
{
    public function addMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = AddMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());

        $handler = new AddMasterTemplateHandler($this->userRepo, $this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = ChangeMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateId($command->getId());

        $handler = new ChangeMasterTemplateHandler($this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeMasterTemplateContents(\stdClass $jsonCommand): bool
    {
        $command = ChangeMasterTemplateContent::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($command->getTemplateId());
        $this->permission->CheckMasterTemplateId($command->getMasterTemplateId());

        $handler = new ChangeMasterTemplateContentHandler($this->templateRepo, $this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = DeleteMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateId($command->getId());

        $handler = new DeleteMasterTemplateHandler($this->templateRepo, $this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = OrderMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckMasterTemplateIds($command->getSequence());

        $handler = new OrderMasterTemplateHandler($this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
