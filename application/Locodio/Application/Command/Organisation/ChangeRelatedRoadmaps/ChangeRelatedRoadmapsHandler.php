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

namespace App\Locodio\Application\Command\Organisation\ChangeRelatedRoadmaps;

use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class ChangeRelatedRoadmapsHandler
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

    public function go(ChangeRelatedRoadmaps $command): bool
    {
        $model = $this->projectRepo->getById($command->getId());
        $model->setRelatedRoadMaps($command->getRelatedRoadmaps());
        $this->projectRepo->save($model);

        return true;
    }
}
