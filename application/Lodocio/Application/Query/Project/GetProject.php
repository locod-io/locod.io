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
use App\Lodocio\Domain\Model\Project\DocProjectRepository;

class GetProject
{
    public function __construct(
        protected DocProjectRepository $docProjectRepository,
    ) {
    }

    public function ById(int $projectId): DocProjectReadModel
    {
        return DocProjectReadModel::hydrateFromModel($this->docProjectRepository->getById($projectId));
    }

}
