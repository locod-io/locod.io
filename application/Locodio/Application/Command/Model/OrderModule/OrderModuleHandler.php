<?php

namespace App\Locodio\Application\Command\Model\OrderModule;

use App\Locodio\Domain\Model\Model\ModuleRepository;

class OrderModuleHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected ModuleRepository $moduleRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderModule $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->moduleRepo->getById($sequenceId);
            $model->setSequence($sequence);
            $this->moduleRepo->save($model);
            $sequence++;
        }
        return true;
    }
}
