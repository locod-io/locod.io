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

use App\Linear\Application\Query\Readmodel\IssueCacheReadModelCollection;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiReadModel;
use App\Lodocio\Application\Query\Wiki\ReadModel\WikiReadModelCollection;

trait GetWikiTrait
{
    public function getWikiByDocProjectId(int $docProjectId): WikiReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckDocProjectId($docProjectId);

        $query = new GetWiki(
            $this->docProjectRepository,
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->wikiNodeStatusRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder,
            $this->entityManager
        );

        return $query->ByDocProject($docProjectId);
    }

    /**
     * @throws \Exception
     */
    public function getWikiById(int $id): WikiReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($id);
        $query = new GetWiki(
            $this->docProjectRepository,
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->wikiNodeStatusRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder,
            $this->entityManager
        );
        return $query->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getFullWikiById(int $id): WikiReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($id);
        $query = new GetWiki(
            $this->docProjectRepository,
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->wikiNodeStatusRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder,
            $this->entityManager
        );
        return $query->FullById($id);
    }

    /**
     * @throws \Exception
     */
    public function getIssuesByWikiId(int $id): IssueCacheReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($id);
        $query = new GetWiki(
            $this->docProjectRepository,
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->wikiNodeStatusRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder,
            $this->entityManager
        );
        return $query->IssuesById($id);
    }

    public function renderWikiPdf(int $id): WikiReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_VIEWER','ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($id);
        $query = new GetWiki(
            $this->docProjectRepository,
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->wikiNodeStatusRepository,
            $this->linearConfig,
            $this->twig,
            $this->uploadFolder,
            $this->entityManager
        );

        return $query->renderWikiPdf($id);
    }

}
