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

use App\Locodio\Application\Command\Model\AddTemplate\AddTemplate;
use App\Locodio\Application\Command\Model\AddTemplate\AddTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeTemplate\ChangeTemplate;
use App\Locodio\Application\Command\Model\ChangeTemplate\ChangeTemplateHandler;
use App\Locodio\Application\Command\Model\ChangeTemplateContent\ChangeTemplateContent;
use App\Locodio\Application\Command\Model\ChangeTemplateContent\ChangeTemplateContentHandler;
use App\Locodio\Application\Command\Model\DeleteTemplate\DeleteTemplate;
use App\Locodio\Application\Command\Model\DeleteTemplate\DeleteTemplateHandler;
use App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate\ExportTemplateToMasterTemplate;
use App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate\ExportTemplateToMasterTemplateHandler;
use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplates;
use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplatesHandler;
use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplate;
use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplateHandler;

trait model_template_command
{
    public function addTemplate(\stdClass $jsonCommand): bool
    {
        $command = AddTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new AddTemplateHandler($this->projectRepo, $this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeTemplate(\stdClass $jsonCommand): bool
    {
        $command = ChangeTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($command->getId());

        $handler = new ChangeTemplateHandler($this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeTemplateContents(\stdClass $jsonCommand): bool
    {
        $command = ChangeTemplateContent::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($command->getTemplateId());
        $this->permission->CheckMasterTemplateId($command->getMasterTemplateId());

        $handler = new ChangeTemplateContentHandler($this->templateRepo, $this->masterTemplateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderTemplate(\stdClass $jsonCommand): bool
    {
        $command = OrderTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateIds($command->getSequence());

        $handler = new OrderTemplateHandler($this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function deleteTemplate(\stdClass $jsonCommand): bool
    {
        $command = DeleteTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($command->getId());

        $handler = new DeleteTemplateHandler($this->templateRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function importTemplatesFromMasterTemplates(\stdClass $jsonCommand): bool
    {
        $command = ImportTemplatesFromMasterTemplates::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());
        $this->permission->CheckMasterTemplateIds($command->getMasterTemplateIds());

        $handler = new ImportTemplatesFromMasterTemplatesHandler(
            $this->projectRepo,
            $this->masterTemplateRepo,
            $this->templateRepo
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function exportTemplateToMasterTemplate(\stdClass $jsonCommand): bool
    {
        $command = ExportTemplateToMasterTemplate::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckUserId($command->getUserId());
        $this->permission->CheckTemplateId($command->getTemplateId());

        $handler = new ExportTemplateToMasterTemplateHandler(
            $this->userRepo,
            $this->masterTemplateRepo,
            $this->templateRepo
        );

        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
