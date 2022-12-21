<?php

declare(strict_types=1);

namespace App\Locodio\Application\Command\Model\AddModelStatus;

use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddModelStatusHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected ModelStatusRepository $modelStatusRepo,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(AddModelStatus $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());

        if ($command->isFinal() || $command->isStart()) {
            $modelStatus = $this->modelStatusRepo->getByProject($project);
            foreach ($modelStatus as $status) {
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

        $model = ModelStatus::make(
            $project,
            $this->modelStatusRepo->nextIdentity(),
            $command->getName(),
            $command->getColor(),
            $command->isStart(),
            $command->isFinal(),
        );
        $lastSequence = $this->modelStatusRepo->getMaxSequence($project)->getSequence() + 1;
        $model->setSequence($lastSequence);
        $this->modelStatusRepo->save($model);
        return true;
    }
}
