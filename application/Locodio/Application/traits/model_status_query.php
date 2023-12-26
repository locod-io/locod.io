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
use App\Locodio\Domain\Model\User\UserRole;

trait model_status_query
{
    public function getModelStatusByProject(int $projectId): ModelStatusRMCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckProjectId($projectId);

        $query = new GetModelStatus($this->projectRepo, $this->modelStatusRepo, $this->documentorRepository);
        return $query->ByProject($projectId);
    }

    public function getModelStatusWorkflowByProject(int $projectId): ModelStatusWorkflow
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckProjectId($projectId);

        $query = new GetModelStatus($this->projectRepo, $this->modelStatusRepo, $this->documentorRepository);
        return $query->WorkflowByProject($projectId);
    }

    public function getModelStatusById(int $id): ModelStatusRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckModelStatusId($id);

        $query = new GetModelStatus($this->projectRepo, $this->modelStatusRepo);
        return $query->ById($id);
    }
}
