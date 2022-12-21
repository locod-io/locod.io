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

namespace App\Locodio\Application\Command\Organisation\UploadProjectLogo;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadProjectLogo
{
    public function __construct(
        protected int          $userId,
        protected int          $projectId,
        protected UploadedFile $file,
        protected string       $uploadsFolder,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
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
