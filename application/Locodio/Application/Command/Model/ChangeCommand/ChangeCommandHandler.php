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

namespace App\Locodio\Application\Command\Model\ChangeCommand;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;

class ChangeCommandHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected CommandRepository     $commandRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeCommand $command): bool
    {
        $model = $this->commandRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($model->getDocumentor())) {
            return false;
        }

        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $model->change($domainModel, $command->getName(), $command->getNamespace(), $command->getMapping());
        $this->commandRepo->save($model);

        return true;
    }
}
