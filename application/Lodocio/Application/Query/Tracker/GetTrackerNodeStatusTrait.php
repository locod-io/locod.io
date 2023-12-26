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

use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeStatusReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeStatusWorkflow;

trait GetTrackerNodeStatusTrait
{
    /**
     * @throws \Exception
     */
    public function getTrackerNodeStatusById(int $id): TrackerNodeStatusReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckTrackerNodeStatusId($id);
        $query = new GetTrackerNodeStatus(
            $this->trackerRepository,
            $this->trackerNodeStatusRepository
        );
        return $query->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getTrackerNodeStatusWorkflow(int $trackerId): TrackerNodeStatusWorkflow
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckTrackerId($trackerId);
        $query = new GetTrackerNodeStatus(
            $this->trackerRepository,
            $this->trackerNodeStatusRepository
        );
        return $query->WorkflowByTracker($trackerId);
    }

}
