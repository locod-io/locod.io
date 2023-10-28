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

use App\Locodio\Application\Query\Linear\Readmodel\IssueCacheReadModelCollection;
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

        $GetOrganisation = new GetOrganisation($this->organisationRepo, $this->linearConfig);

        return $GetOrganisation->TeamsByOrganisation($id);
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

}
