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

use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeReadModel;

trait GetTrackerNodeTrait
{
    /**
     * @throws \Exception
     */
    public function getTrackerNodeById(int $id): TrackerNodeReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeId($id);
        $query = new GetTrackerNode(
            $this->trackerRepository,
            $this->trackerNodeRepository,
            $this->linearConfig
        );
        return $query->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getFullTrackerNodeById(int $id): TrackerNodeReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeId($id);
        $query = new GetTrackerNode(
            $this->trackerRepository,
            $this->trackerNodeRepository,
            $this->linearConfig
        );
        return $query->FullById($id);
    }

    /**
     * @throws \Exception
     */
    public function getIssuesByNodeId(int $id): IssueReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeId($id);
        $query = new GetTrackerNode(
            $this->trackerRepository,
            $this->trackerNodeRepository,
            $this->linearConfig
        );
        return $query->IssuesById($id);
    }

}
