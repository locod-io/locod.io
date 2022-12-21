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

namespace App\Locodio\Application\Command\Model\CreateSampleProject;

class CreateSampleProjectById
{
    public function __construct(
        protected int $projectId,
    ) {
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
