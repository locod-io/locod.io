<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lodocio\Application\Command\Tracker\DeleteTrackerFile;

use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeFileRepository;

class DeleteTrackerFileHandler
{
    public function __construct(
        protected TrackerNodeFileRepository $fileRepository,
        protected string                    $uploadFolder,
    ) {
    }

    public function go(DeleteTrackerFile $command): bool
    {
        $trackerFile = $this->fileRepository->getById($command->getId());
        $sourceFile = $this->uploadFolder . $trackerFile->getSrcPath();
        if (file_exists($sourceFile)) {
            unlink($sourceFile);
        }
        $this->fileRepository->delete($trackerFile);
        return true;
    }
}
