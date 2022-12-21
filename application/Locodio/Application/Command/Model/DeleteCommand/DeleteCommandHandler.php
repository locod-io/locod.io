<?php

namespace App\Locodio\Application\Command\Model\DeleteCommand;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\CommandRepository;

class DeleteCommandHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected CommandRepository $commandRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteCommand $command): bool
    {
        $command = $this->commandRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($command->getDocumentor())) {
            return false;
        }

        $this->commandRepo->delete($command);
        return true;
    }
}
