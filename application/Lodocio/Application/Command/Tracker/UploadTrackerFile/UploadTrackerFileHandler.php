<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lodocio\Application\Command\Tracker\UploadTrackerFile;

use App\Lodocio\Application\Helper\SimpleImage;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFile;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFileRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Uid\Uuid;

class UploadTrackerFileHandler
{
    public function __construct(
        protected TrackerNodeRepository     $trackerNodeRepository,
        protected TrackerNodeFileRepository $fileRepository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function go(UploadTrackerFile $command): bool
    {
        $node = $this->trackerNodeRepository->getById($command->getTrackerNodeId());
        $file = $command->getFile();
        $extension = strtolower($file->guessExtension());

        // -- check the extension and filesize
        if (!($extension === 'png'
            || $extension === 'jpeg'
            || $extension === 'jpg'
            || $extension === 'gif')) {
            throw new \Exception('Could not save the uploaded file');
            return false;
        }

        $filesize = filesize($file->getRealPath());
        if ($filesize > 10000000) {
            throw new \Exception('Could not save the uploaded file');
            return false;
        }

        // -- test if the upload is a real image
        $image = SimpleImage::load($file->getRealPath());

        $uploadFolder = 'O-' . $node->getTracker()->getProject()->getProject()->getOrganisation()->getId() .
            '/P-' . $node->getTracker()->getProject()->getId() .
            '/T-' . $node->getTracker()->getId() . '/';

        // -- make the folder
        if (!file_exists($command->getUploadsFolder() . $uploadFolder)) {
            mkdir($command->getUploadsFolder() . $uploadFolder, 0777, true);
        }

        $fileUuid = $this->fileRepository->nextIdentity()->toRfc4122();
        $uploadFile = $fileUuid . '.' . $extension;
        try {
            $file->move($command->getUploadsFolder() . $uploadFolder, $uploadFile);
        } catch (FileException $e) {
            throw new \Exception('Could not copy the uploaded file');
            return false;
        }

        $trackerFile = TrackerNodeFile::make(
            $node,
            Uuid::fromString($fileUuid),
            $this->fileRepository->getNextArtefactId($node->getTracker()),
            0,
            $file->getClientOriginalName(),
            $file->getClientOriginalName(),
            $uploadFolder.$uploadFile,
            '',
        );
        $this->fileRepository->save($trackerFile);

        return true;
    }

}
