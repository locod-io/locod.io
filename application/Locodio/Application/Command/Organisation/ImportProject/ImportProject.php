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

namespace App\Locodio\Application\Command\Organisation\ImportProject;

class ImportProject
{
    public function __construct(
        protected string    $projectUuid,
        protected \stdClass $importProject
    ) {
    }

    public function getProjectUuid(): string
    {
        return $this->projectUuid;
    }

    public function getImportProject(): \stdClass
    {
        return $this->importProject;
    }
}
