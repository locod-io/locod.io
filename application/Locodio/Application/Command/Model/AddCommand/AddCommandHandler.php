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

namespace App\Locodio\Application\Command\Model\AddCommand;

use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddCommandHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected CommandRepository     $commandRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddCommand $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());

        $commands = $this->commandRepo->getByProject($project);
        foreach ($commands as $commandModel) {
            $commandModel->setSequence($commandModel->getSequence() + 1);
            $this->commandRepo->save($commandModel);
        }

        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $model = Command::make($project, $this->commandRepo->nextIdentity(), $domainModel, $command->getName());
        $model->setArtefactId($this->commandRepo->getNextArtefactId($project));

        $this->commandRepo->save($model);

        return true;
    }
}
