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

use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeStatusReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeStatusWorkflow;

trait GetWikiNodeStatusTrait
{
    /**
     * @throws \Exception
     */
    public function getWikiNodeStatusById(int $id): WikiNodeStatusReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeStatusId($id);
        $query = new GetWikiNodeStatus(
            $this->wikiRepository,
            $this->wikiNodeStatusRepository
        );
        return $query->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getWikiNodeStatusWorkflow(int $wikiId): WikiNodeStatusWorkflow
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($wikiId);
        $query = new GetWikiNodeStatus(
            $this->wikiRepository,
            $this->wikiNodeStatusRepository
        );
        return $query->WorkflowByWiki($wikiId);
    }

}
