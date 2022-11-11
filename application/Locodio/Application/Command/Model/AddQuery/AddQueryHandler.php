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

namespace App\Locodio\Application\Command\Model\AddQuery;

use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\Query;

use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use Doctrine\ORM\EntityNotFoundException;

class AddQueryHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected QueryRepository       $queryRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    /**
     * @throws EntityNotFoundException
     */
    public function go(AddQuery $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());

        $queries = $this->queryRepo->getByProject($project);
        foreach ($queries as $query) {
            $query->setSequence($query->getSequence()+1);
            $this->queryRepo->save($query);
        }

        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $model = Query::make($project, $this->queryRepo->nextIdentity(), $domainModel, $command->getName());
        $this->queryRepo->save($model);

        return true;
    }
}
