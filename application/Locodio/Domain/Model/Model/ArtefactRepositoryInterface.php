<?php

namespace App\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Organisation\Project;

interface ArtefactRepositoryInterface extends BaseRepositoryInteface
{
    public function getNextArtefactId(Project $project): int;

}
