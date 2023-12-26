<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lodocio\Application\Command\Wiki\UploadWikiFile;

use App\Lodocio\Application\Helper\SimpleImage;
use App\Lodocio\Domain\Model\Wiki\WikiNodeFile;
use App\Lodocio\Domain\Model\Wiki\WikiNodeFileRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Uid\Uuid;

class UploadWikiFileHandler
{
    public function __construct(
        protected WikiNodeRepository     $wikiNodeRepository,
        protected WikiNodeFileRepository $fileRepository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function go(UploadWikiFile $command): bool
    {
        $node = $this->wikiNodeRepository->getById($command->getWikiNodeId());
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

        $uploadFolder = 'O-' . $node->getWiki()->getProject()->getProject()->getOrganisation()->getId() .
            '/P-' . $node->getWiki()->getProject()->getId() .
            '/W-' . $node->getWiki()->getId() . '/';

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

        $wikiFile = WikiNodeFile::make(
            $node,
            Uuid::fromString($fileUuid),
            $this->fileRepository->getNextArtefactId($node->getWiki()),
            0,
            $file->getClientOriginalName(),
            $file->getClientOriginalName(),
            $uploadFolder . $uploadFile,
            '',
        );
        $this->fileRepository->save($wikiFile);

        return true;
    }

}
