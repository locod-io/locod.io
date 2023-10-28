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

namespace App\Lodocio\Application\Command\Project\ChangeDocProject;

use App\Locodio\Infrastructure\Database\ProjectRepository;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;

class ChangeDocProjectHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository $projectRepository,
        protected DocProjectRepository $docProjectRepository,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeDocProject $command): bool
    {
        $project = $this->projectRepository->getById($command->getProjectId());
        $docProject = $this->docProjectRepository->getByProject($project);
        $docProject->change($command->getName(), $command->getCode(), '#' . $command->getColor());
        $this->docProjectRepository->save($docProject);
        return true;
    }
}
