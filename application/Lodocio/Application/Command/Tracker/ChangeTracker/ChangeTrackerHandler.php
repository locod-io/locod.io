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

namespace App\Lodocio\Application\Command\Tracker\ChangeTracker;

use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class ChangeTrackerHandler
{
    public function __construct(
        protected TrackerRepository $trackerRepo
    ) {
    }

    public function go(ChangeTracker $command): bool
    {
        $model = $this->trackerRepo->getById($command->getId());
        $model->change(
            $command->getName(),
            $command->getCode(),
            '#'.$command->getColor(),
            $command->getDescription(),
            $command->getRelatedTeams(),
            $command->getSlug(),
            $command->isPublic(),
            $command->showOnlyFinalNodes()
        );
        $id = $this->trackerRepo->save($model);
        return true;
    }
}
