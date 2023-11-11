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

namespace App\Lodocio\Application\Command\Tracker\ChangeNodeDescription;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;

class ChangeNodeDescriptionHandler
{
    public function __construct(
        protected TrackerNodeRepository $trackerNodeRepository
    ) {
    }

    public function go(ChangeNodeDescription $command): bool
    {
        $model = $this->trackerNodeRepository->getById($command->getId());
        if (!$model->getTrackerNodeStatus()->isFinal()) {
            $model->setDescription($command->getDescription());
            $id = $this->trackerNodeRepository->save($model);
        }
        return true;
    }
}
