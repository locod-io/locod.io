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

use App\Locodio\Application\Query\Organisation\GetProject;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;

trait organisation_project_query
{
    public function getProjectById(int $id): ProjectRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($id);

        $GetProject = new GetProject($this->projectRepo, $this->domainModelRepo, $this->documentorRepository);
        return $GetProject->ById($id);
    }
}
