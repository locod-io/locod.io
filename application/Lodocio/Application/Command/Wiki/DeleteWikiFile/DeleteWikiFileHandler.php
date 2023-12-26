<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lodocio\Application\Command\Wiki\DeleteWikiFile;

use App\Lodocio\Infrastructure\Database\Wiki\WikiNodeFileRepository;

class DeleteWikiFileHandler
{
    public function __construct(
        protected WikiNodeFileRepository $fileRepository,
        protected string                 $uploadFolder,
    ) {
    }

    public function go(DeleteWikiFile $command): bool
    {
        $wikiFile = $this->fileRepository->getById($command->getId());
        $sourceFile = $this->uploadFolder . $wikiFile->getSrcPath();
        if (file_exists($sourceFile)) {
            unlink($sourceFile);
        }
        $this->fileRepository->delete($wikiFile);
        return true;
    }
}
