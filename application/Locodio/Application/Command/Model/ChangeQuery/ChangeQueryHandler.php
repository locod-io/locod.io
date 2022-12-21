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

namespace App\Locodio\Application\Command\Model\ChangeQuery;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;

class ChangeQueryHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected QueryRepository       $queryRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeQuery $command): bool
    {
        $model = $this->queryRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($model->getDocumentor())) {
            return false;
        }

        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $model->change($domainModel, $command->getName(), $command->getNamespace(), $command->getMapping());
        $this->queryRepo->save($model);
        return true;
    }
}
