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

namespace App\Locodio\Application\Command\Model\ChangeModelSettings;

use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Locodio\Domain\Model\Model\ModelSettingsRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class ChangeModelSettingsHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository $projectRepo,
        protected ModelSettingsRepository $modelSettingsRepo,
    ) {
    }

    public function go(ChangeModelSettings $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());
        if (is_null($project->getModelSettings())) {
            $modelSettings = ModelSettings::make(
                $project,
                $this->modelSettingsRepo->nextIdentity(),
                $command->getDomainLayer(),
                $command->getApplicationLayer(),
                $command->getInfrastructureLayer(),
            );
            $this->modelSettingsRepo->save($modelSettings);
            $project->setModelSettings($modelSettings);
            $this->projectRepo->save($project);
        } else {
            $modelSettings = $this->modelSettingsRepo->getById($command->getId());
            $modelSettings->change(
                $command->getDomainLayer(),
                $command->getApplicationLayer(),
                $command->getInfrastructureLayer(),
            );
            $this->modelSettingsRepo->save($modelSettings);
        }
        return true;
    }
}
