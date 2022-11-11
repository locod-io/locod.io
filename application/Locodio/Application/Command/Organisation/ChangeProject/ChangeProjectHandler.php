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

namespace App\Locodio\Application\Command\Organisation\ChangeProject;

use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class ChangeProjectHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository $projectRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeProject $command): bool
    {
        $project = $this->projectRepo->getById($command->getId());
        $project->change($command->getName(), $command->getCode(), '#' . $command->getColor());
        $project->setLayers(
            trim($command->getDomainLayer()),
            trim($command->getApplicationLayer()),
            trim($command->getInfrastructureLayer())
        );
        $this->projectRepo->save($project);
        return true;
    }
}
