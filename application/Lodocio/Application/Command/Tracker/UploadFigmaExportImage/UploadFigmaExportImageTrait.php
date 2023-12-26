<?php

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\UploadFigmaExportImage;

trait UploadFigmaExportImageTrait
{
    public function uploadFigmaExportImage(\stdClass $jsonCommand): bool
    {
        $command = UploadFigmaExportImage::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);

        $commandHandler = new UploadFigmaExportImageHandler(
            $this->trackerNodeRepository,
            $this->trackerNodeFileRepository,
            $this->figmaConfig,
            $this->uploadFolder,
        );

        $result = $commandHandler->uploadFigmaFiles($command);
        $this->entityManager->flush();

        return true;
    }

}
