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

namespace App\Locodio\Application\Command\Organisation\ChangeRelatedProjects;

use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class ChangeRelatedProjectsHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository $projectRepo
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeRelatedProjects $command): bool
    {
        $model = $this->projectRepo->getById($command->getId());
        $model->setRelatedProjects($command->getRelatedProjects());
        $this->projectRepo->save($model);
        return true;
    }

}
