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

namespace App\Linear\Application\Query\traits;

use App\Linear\Application\Query\GetIssues;
use App\Linear\Application\Query\GetProject;
use App\Linear\Application\Query\GetTeams;
use App\Linear\Application\Query\Readmodel\IssueReadModel;
use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use App\Linear\Application\Query\Readmodel\ProjectReadModelCollection;
use App\Linear\Application\Query\Readmodel\TeamReadModelCollection;

trait linear_queries
{
    // ——————————————————————————————————————————————————————————————————————————
    // Linear projects, teams & issues
    // ——————————————————————————————————————————————————————————————————————————

    /**
     * @throws \Exception
     */
    public function getLinearProjects(): ProjectReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $getProject = new GetProject($this->projectRepo, $this->linearConfig);

        return $getProject->All();
    }

    /**
     * @throws \Exception
     */
    public function getLinearProjectById(int $id, string $uuid): array
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckProjectId($id);

        $getProject = new GetProject($this->projectRepo, $this->linearConfig);

        return $getProject->ByUuid($id, $uuid);
    }

    /**
     * @throws \Exception
     */
    public function getLinearTeams(): TeamReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $getTeams = new GetTeams($this->linearConfig);

        return $getTeams->All();
    }

    /**
     * @throws \Exception
     */
    public function getLinearIssuesByTeam(string $id): IssueReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $getIssues = new GetIssues($this->linearConfig);

        return $getIssues->ByTeam($id);
    }

    /**
     * @throws \Exception
     */
    public function getLinearIssueById(string $id): IssueReadModel
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $getIssues = new GetIssues($this->linearConfig);

        return $getIssues->ByIssueId($id);
    }


}
