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

namespace App\Locodio\Application\Query\Linear\traits;

use App\Locodio\Application\Query\Linear\GetIssues;
use App\Locodio\Application\Query\Linear\GetTeams;
use App\Locodio\Application\Query\Linear\Readmodel\IssueReadModel;
use App\Locodio\Application\Query\Linear\Readmodel\IssueReadModelCollection;
use App\Locodio\Application\Query\Linear\Readmodel\TeamReadModelCollection;

trait linear_queries
{
    // ——————————————————————————————————————————————————————————————————————————
    // Linear teams & issues
    // ——————————————————————————————————————————————————————————————————————————

    /**
     * @throws \Exception
     */
    public function getLinearTeams(): TeamReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $getTeams = new GetTeams($this->linearConfig);

        return $getTeams->All();
    }

    /**
     * @throws \Exception
     */
    public function getLinearIssuesByTeam(string $id): IssueReadModelCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $getIssues = new GetIssues($this->linearConfig);

        return $getIssues->ByTeam($id);
    }

    /**
     * @throws \Exception
     */
    public function getLinearIssueById(string $id): IssueReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $getIssues = new GetIssues($this->linearConfig);

        return $getIssues->ByIssueId($id);
    }

}
