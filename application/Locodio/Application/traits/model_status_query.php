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

use App\Locodio\Application\Query\Model\GetModelStatus;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRM;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusWorkflow;

trait model_status_query
{
    public function getModelStatusByProject(int $projectId): ModelStatusRMCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($projectId);

        $query = new GetModelStatus($this->projectRepo, $this->modelStatusRepo, $this->documentorRepository);
        return $query->ByProject($projectId);
    }

    public function getModelStatusWorkflowByProject(int $projectId): ModelStatusWorkflow
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($projectId);

        $query = new GetModelStatus($this->projectRepo, $this->modelStatusRepo, $this->documentorRepository);
        return $query->WorkflowByProject($projectId);
    }

    public function getModelStatusById(int $id): ModelStatusRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckModelStatusId($id);

        $query = new GetModelStatus($this->projectRepo, $this->modelStatusRepo);
        return $query->ById($id);
    }
}
