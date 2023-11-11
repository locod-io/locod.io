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

namespace App\Lodocio\Application\Query\Project;

use App\Lodocio\Application\Query\Project\ReadModel\DocProjectReadModel;

trait GetProjectTrait
{
    /**
     * @throws \Exception
     */
    public function getDocProjectById(int $id): DocProjectReadModel
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDocProjectId($id);
        $query = new GetProject($this->docProjectRepository);
        return $query->ById($id);
    }

}
