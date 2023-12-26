<?php

namespace App\Lodocio\Application\Command\Wiki\LinkProjectDocument;

use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;

class LinkProjectDocumentHandler
{
    public function __construct(
        protected WikiRepository          $wikiRepository,
        protected WikiNodeRepository      $wikiNodeRepository,
        protected WikiNodeGroupRepository $wikiNodeGroupRepository,
        protected LinearConfig            $linearConfig,
    ) {
    }

    public function go(LinkProjectDocument $command)
    {

    }

}
