<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\UploadTrackerFile;

trait UploadTrackerFileTrait
{
    public function uploadImageForTrackerNode(UploadTrackerFile $command): bool
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckTrackerNodeId($command->getTrackerNodeId());

        $handler = new UploadTrackerFileHandler(
            $this->trackerNodeRepository,
            $this->trackerNodeFileRepository,
        );

        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
