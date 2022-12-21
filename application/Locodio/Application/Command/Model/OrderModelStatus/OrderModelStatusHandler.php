<?php

namespace App\Locodio\Application\Command\Model\OrderModelStatus;

use App\Locodio\Domain\Model\Model\ModelStatusRepository;

class OrderModelStatusHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected ModelStatusRepository $modelStatusRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderModelStatus $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->modelStatusRepo->getById($sequenceId);
            $model->setSequence($sequence);
            $this->modelStatusRepo->save($model);
            $sequence++;
        }
        return true;
    }
}
