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

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadTrackerFile
{
    public function __construct(
        protected int          $trackerNodeId,
        protected UploadedFile $file,
        protected string       $uploadsFolder,
    ) {
    }

    public function getTrackerNodeId(): int
    {
        return $this->trackerNodeId;
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
