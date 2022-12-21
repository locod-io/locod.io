<?php

namespace App\Locodio\Application\Command\Model\DeleteEnumOption;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;

class DeleteEnumOptionHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected EnumOptionRepository $enumOptionRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteEnumOption$command): bool
    {
        $enumOption =  $this->enumOptionRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($enumOption->getEnum()->getDocumentor())) {
            return false;
        }

        $this->enumOptionRepo->delete($enumOption);

        return true;
    }
}
