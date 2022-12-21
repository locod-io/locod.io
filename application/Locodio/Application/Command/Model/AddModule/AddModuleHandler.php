<?php

namespace App\Locodio\Application\Command\Model\AddModule;

use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddModuleHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository $projectRepo,
        protected ModuleRepository  $moduleRepo,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(AddModule $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());
        $model = Module::make(
            $project,
            $this->moduleRepo->nextIdentity(),
            $command->getName(),
            $command->getNamespace(),
        );
        $lastSequence = $this->moduleRepo->getMaxSequence($project)->getSequence()+1;
        $model->setSequence($lastSequence);
        $this->moduleRepo->save($model);
        return true;
    }
}
