<?php

namespace App\Lodocio\Application\Command\Tracker\LinkProjectDocument;

use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;

class LinkProjectDocumentHandler
{
    public function __construct(
        protected TrackerRepository          $trackerRepository,
        protected TrackerNodeRepository      $trackerNodeRepository,
        protected TrackerNodeGroupRepository $trackerNodeGroupRepository,
        protected LinearConfig               $linearConfig,
    ) {
    }

    public function go(LinkProjectDocument $command)
    {

    }

}
