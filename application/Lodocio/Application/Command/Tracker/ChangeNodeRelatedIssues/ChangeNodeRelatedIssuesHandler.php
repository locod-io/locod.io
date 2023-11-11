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

namespace App\Lodocio\Application\Command\Tracker\ChangeNodeRelatedIssues;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;

class ChangeNodeRelatedIssuesHandler
{
    public function __construct(
        protected TrackerNodeRepository $trackerNodeRepository
    ) {
    }

    public function go(ChangeNodeRelatedIssues $command): bool
    {
        $model = $this->trackerNodeRepository->getById($command->getId());
        $model->setRelatedIssues($command->getRelatedIssues());
        $id = $this->trackerNodeRepository->save($model);
        return true;
    }
}
