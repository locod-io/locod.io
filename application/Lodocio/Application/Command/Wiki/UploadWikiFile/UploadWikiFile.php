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

namespace App\Lodocio\Application\Command\Wiki\UploadWikiFile;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadWikiFile
{
    public function __construct(
        protected int          $wikiNodeId,
        protected UploadedFile $file,
        protected string       $uploadsFolder,
    ) {
    }

    public function getWikiNodeId(): int
    {
        return $this->wikiNodeId;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getUploadsFolder(): string
    {
        return $this->uploadsFolder;
    }

}
