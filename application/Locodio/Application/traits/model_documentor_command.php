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

use App\Locodio\Application\Command\Model\ChangeDocumentor\ChangeDocumentHandler;
use App\Locodio\Application\Command\Model\ChangeDocumentor\ChangeDocumentor;
use App\Locodio\Application\Command\Model\ChangeDocumentor\ChangeDocumentorStatus;
use App\Locodio\Application\Command\Model\RemoveDocumenterImage\RemoveDocumentorImage;
use App\Locodio\Application\Command\Model\RemoveDocumenterImage\RemoveDocumentorImageHandler;
use App\Locodio\Application\Command\Model\UploadDocumentorImage\UploadDocumentorImage;
use App\Locodio\Application\Command\Model\UploadDocumentorImage\UploadDocumentorImageHandler;

trait model_documentor_command
{
    public function changeDocumentor(\stdClass $jsonCommand): bool
    {
        $command = ChangeDocumentor::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModelStatusId($command->getStatusId());
        $this->permission->CheckDocumentorId($command->getId());

        $handler = new ChangeDocumentHandler(
            $this->security,
            $this->documentorRepo,
            $this->modelStatusRepo
        );
        $handler->go($command);
        $this->entityManager->flush();
        return true;
    }

    public function changeDocumentorStatus(\stdClass $jsonCommand): bool
    {
        $command = ChangeDocumentorStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModelStatusId($command->getStatusId());
        $this->permission->CheckDocumentorId($command->getId());

        $handler = new ChangeDocumentHandler(
            $this->security,
            $this->documentorRepo,
            $this->modelStatusRepo
        );
        $handler->goStatus($command);
        $this->entityManager->flush();
        return true;
    }

    public function uploadImageForDocumentor(UploadDocumentorImage $uploadDocumentorImage): bool
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDocumentorId($uploadDocumentorImage->getDocumentorId());

        $handler = new UploadDocumentorImageHandler(
            $this->documentorRepo,
            $this->moduleRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo,
        );

        $result = $handler->go($uploadDocumentorImage);
        $this->entityManager->flush();
        return $result;
    }

    public function removeImageForDocumentor(\stdClass $jsonCommand): bool
    {
        $command = RemoveDocumentorImage::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDocumentorId($command->getDocumentorId());

        $handler = new RemoveDocumentorImageHandler($this->documentorRepo);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
