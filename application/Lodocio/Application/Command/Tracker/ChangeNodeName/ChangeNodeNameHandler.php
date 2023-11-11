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

namespace App\Lodocio\Application\Command\Tracker\ChangeNodeName;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;

class ChangeNodeNameHandler
{
    public function __construct(
        protected TrackerNodeRepository $trackerNodeRepository
    ) {
    }

    public function go(ChangeNodeName $command): bool
    {
        $model = $this->trackerNodeRepository->getById($command->getId());
        if(!$model->getTrackerNodeStatus()->isFinal()) {
            $model->setName($command->getName());
            $id = $this->trackerNodeRepository->save($model);
        }
        return true;
    }
}
