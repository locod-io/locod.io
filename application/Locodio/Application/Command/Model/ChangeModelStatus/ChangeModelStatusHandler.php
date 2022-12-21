<?php

declare(strict_types=1);

namespace App\Locodio\Application\Command\Model\ChangeModelStatus;

use App\Locodio\Domain\Model\Model\ModelStatusRepository;

class ChangeModelStatusHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ModelStatusRepository $modelStatusRepo,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeModelStatus $command): bool
    {
        $model = $this->modelStatusRepo->getById($command->getId());

        if ($command->isFinal() || $command->isStart()) {
            $modelStatus = $this->modelStatusRepo->getByProject($model->getProject());
            foreach ($modelStatus as $status) {
                if ($status->getId() !== $model->getId()) {
                    if ($command->isFinal()) {
                        $status->deFinalize();
                        $this->modelStatusRepo->save($status);
                    }
                    if ($command->isStart()) {
                        $status->deStarterize();
                        $this->modelStatusRepo->save($status);
                    }
                }
            }
        }

        $model->change(
            $command->getName(),
            $command->getColor(),
            $command->isStart(),
            $command->isFinal(),
        );
        $this->modelStatusRepo->save($model);
        return true;
    }
}
