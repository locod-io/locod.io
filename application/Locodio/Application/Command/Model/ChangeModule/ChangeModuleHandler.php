<?php

namespace App\Locodio\Application\Command\Model\ChangeModule;

use App\Locodio\Domain\Model\Model\ModuleRepository;

class ChangeModuleHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ModuleRepository $moduleRepo
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeModule $command): bool
    {
        $model = $this->moduleRepo->getById($command->getId());
        $model->change(
            $command->getName(),
            $command->getNamespace(),
        );
        $this->moduleRepo->save($model);
        return true;
    }
}
