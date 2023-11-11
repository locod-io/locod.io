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

namespace App\Lodocio\Application\Command\Tracker\ChangeGroupName;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;

class ChangeGroupNameHandler
{
    public function __construct(
        protected TrackerNodeGroupRepository $trackerNodeGroupRepository
    ) {
    }

    public function go(ChangeGroupName $command): bool
    {
        $model = $this->trackerNodeGroupRepository->getById($command->getId());
        $model->setName($command->getName());
        $id = $this->trackerNodeGroupRepository->save($model);
        return true;
    }
}
