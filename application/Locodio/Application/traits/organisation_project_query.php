<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Query\Linear\Readmodel\DocumentReadModelCollection;
use App\Locodio\Application\Query\Linear\Readmodel\IssueCacheReadModelCollection;
use App\Locodio\Application\Query\Linear\Readmodel\ProjectReadModelCollection;
use App\Locodio\Application\Query\Linear\Readmodel\RoadmapReadModelCollection;
use App\Locodio\Application\Query\Linear\Readmodel\TeamReadModelCollection;
use App\Locodio\Application\Query\Organisation\GetOrganisation;
use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

trait organisation_project_query
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException|\Exception
     */
    public function getProjectById(int $id): ProjectRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProject = new GetProject(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->documentorRepository,
            $this->linearConfig
        );
        return $GetProject->ById($id);
    }

    /**
     * @throws \Exception
     */
    public function getTeamsByOrganisation(int $id): TeamReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->TeamsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getProjectsByOrganisation(int $id): ProjectReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->ProjectsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getRoadmapsByOrganisation(int $id): RoadmapReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->RoadmapsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getFullRoadmapsByOrganisation(int $id):RoadmapReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->FullRoadmapsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getIssuesListByProject(int $id): IssueCacheReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProject = new GetProject(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->documentorRepository,
            $this->linearConfig
        );

        return $GetProject->ListIssuesById($id);
    }

    /**
     * @throws \Exception
     */
    public function getDocumentsByProject(int $id): DocumentReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProject = new GetProject(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->documentorRepository,
            $this->linearConfig
        );

        return $GetProject->ListDocumentsById($id);
    }


    /**
     * @throws \Exception
     */
    public function getRoadmapsByProject(int $id): RoadmapReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProject = new GetProject(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->documentorRepository,
            $this->linearConfig
        );

        return $GetProject->RoadmapsById($id);
    }

}
