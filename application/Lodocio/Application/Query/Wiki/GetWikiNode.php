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

namespace App\Lodocio\Application\Query\Wiki;

use App\Linear\Application\Query\GetIssues;
use App\Linear\Application\Query\LinearConfig;
use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeReadModel;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;

class GetWikiNode
{
    public function __construct(
        protected WikiRepository     $wikiRepository,
        protected WikiNodeRepository $wikiNodeRepository,
        protected LinearConfig          $linearConfig,
    ) {
    }

    public function ById(int $id): WikiNodeReadModel
    {
        return WikiNodeReadModel::hydrateFromModel($this->wikiNodeRepository->getById($id));
    }

    public function FullById(int $id): WikiNodeReadModel
    {
        $wikiNode = $this->wikiNodeRepository->getById($id);
        $readModel = WikiNodeReadModel::hydrateFromModel($this->wikiNodeRepository->getById($id));
        return $readModel;
    }

    public function IssuesById(int $id): IssueReadModelCollection
    {
        $wikiNode = $this->wikiNodeRepository->getById($id);
        $collection = new IssueReadModelCollection();
        if (strlen($wikiNode->getWiki()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($wikiNode->getWiki()->getProject()->getOrganisation()->getLinearApiKey());
        }
        $getIssue = new GetIssues($this->linearConfig);
        foreach ($wikiNode->getRelatedIssues() as $linearIssue) {
            $issue = $getIssue->ByIssueId($linearIssue['id']);
            $collection->addItem($issue);
        }
        return $collection;
    }

}
