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

namespace App\Locodio\Application\Command\Model\ChangeDomainModel;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\ModuleRepository;

class ChangeDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected ModuleRepository      $moduleRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeDomainModel $command): bool
    {
        $model = $this->domainModelRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($model->getDocumentor())) {
            return false;
        }

        $module = $this->moduleRepo->getById($command->getModuleId());
        $model->change($command->getName(), $command->getNamespace(), $command->getRepository());
        $model->setModule($module);
        $this->domainModelRepo->save($model);

        return true;
    }
}
