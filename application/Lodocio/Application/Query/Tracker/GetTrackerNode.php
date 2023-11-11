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

use App\Locodio\Application\Query\Linear\GetIssues;
use App\Locodio\Application\Query\Linear\LinearConfig;
use App\Locodio\Application\Query\Linear\Readmodel\IssueReadModelCollection;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeReadModel;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class GetTrackerNode
{
    public function __construct(
        protected TrackerRepository     $trackerRepository,
        protected TrackerNodeRepository $trackerNodeRepository,
        protected LinearConfig          $linearConfig,
    ) {
    }

    public function ById(int $id): TrackerNodeReadModel
    {
        return TrackerNodeReadModel::hydrateFromModel($this->trackerNodeRepository->getById($id));
    }

    public function FullById(int $id): TrackerNodeReadModel
    {
        $trackerNode = $this->trackerNodeRepository->getById($id);
        $readModel = TrackerNodeReadModel::hydrateFromModel($this->trackerNodeRepository->getById($id));
        return $readModel;
    }

    public function IssuesById(int $id): IssueReadModelCollection
    {
        $trackerNode = $this->trackerNodeRepository->getById($id);
        $collection = new IssueReadModelCollection();
        if (strlen($trackerNode->getTracker()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($trackerNode->getTracker()->getProject()->getOrganisation()->getLinearApiKey());
        }
        $getIssue = new GetIssues($this->linearConfig);
        foreach ($trackerNode->getRelatedIssues() as $linearIssue) {
            $issue = $getIssue->ByIssueId($linearIssue['id']);
            $collection->addItem($issue);
        }
        return $collection;
    }

}
