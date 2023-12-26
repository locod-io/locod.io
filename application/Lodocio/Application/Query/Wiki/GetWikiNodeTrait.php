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

use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeReadModel;

trait GetWikiNodeTrait
{
    /**
     * @throws \Exception
     */
    public function getWikiNodeById(int $id): WikiNodeReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($id);
        $query = new GetWikiNode(
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->linearConfig
        );
        return $query->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getFullWikiNodeById(int $id): WikiNodeReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($id);
        $query = new GetWikiNode(
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->linearConfig
        );
        return $query->FullById($id);
    }

    /**
     * @throws \Exception
     */
    public function getIssuesByNodeId(int $id): IssueReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($id);
        $query = new GetWikiNode(
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->linearConfig
        );
        return $query->IssuesById($id);
    }

}
