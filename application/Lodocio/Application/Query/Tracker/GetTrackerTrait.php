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

namespace App\Lodocio\Application\Query\Tracker;

use App\Locodio\Application\Query\Linear\Readmodel\IssueCacheReadModelCollection;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModel;

trait GetTrackerTrait
{
    /**
     * @throws \Exception
     */
    public function getTrackerById(int $id): TrackerReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerId($id);
        $query = new GetTracker(
            $this->docProjectRepository,
            $this->trackerRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder
        );
        return $query->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getFullTrackerById(int $id): TrackerReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerId($id);
        $query = new GetTracker(
            $this->docProjectRepository,
            $this->trackerRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder
        );
        return $query->FullById($id);
    }

    /**
     * @throws \Exception
     */
    public function getIssuesByTrackerId(int $id): IssueCacheReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerId($id);
        $query = new GetTracker(
            $this->docProjectRepository,
            $this->trackerRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder
        );
        return $query->IssuesById($id);
    }

    public function renderTrackerPdf(int $id): TrackerReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerId($id);
        $query = new GetTracker(
            $this->docProjectRepository,
            $this->trackerRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder
        );

        return $query->renderTrackerPdf($id);
    }

}
