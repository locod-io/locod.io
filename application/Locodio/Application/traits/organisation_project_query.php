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

use App\Linear\Application\Query\Readmodel\DocumentReadModelCollection;
use App\Linear\Application\Query\Readmodel\IssueCacheReadModelCollection;
use App\Linear\Application\Query\Readmodel\ProjectReadModelCollection;
use App\Linear\Application\Query\Readmodel\RoadmapReadModelCollection;
use App\Linear\Application\Query\Readmodel\TeamReadModelCollection;
use App\Locodio\Application\Query\Organisation\GetOrganisation;
use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Domain\Model\User\UserRole;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

trait organisation_project_query
{
    private $getProject;

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException|\Exception
     */
    public function getProjectById(int $id): ProjectRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
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
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->TeamsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getProjectsByOrganisation(int $id): ProjectReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->ProjectsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getRoadmapsByOrganisation(int $id): RoadmapReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->RoadmapsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getFullRoadmapsByOrganisation(int $id): RoadmapReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckOrganisationId($id);

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->projectRepo, $this->linearConfig);

        return $GetOrganisation->FullRoadmapsByOrganisation($id);
    }

    /**
     * @throws \Exception
     */
    public function getIssuesListByProject(int $id): IssueCacheReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckProjectId($id);

        $this->getProject = new GetProject(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->documentorRepository,
            $this->linearConfig
        );
        $GetProject = $this->getProject;

        return $GetProject->ListIssuesById($id);
    }

    /**
     * @throws \Exception
     */
    public function getDocumentsByProject(int $id): DocumentReadModelCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
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
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
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
