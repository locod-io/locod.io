<?php

namespace App\Locodio\Application\Command\Model\DeleteEnum;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;

class DeleteEnumHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected EnumRepository $enumRepo, protected EnumOptionRepository $enumOptionRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteEnum $command): bool
    {
        $enum = $this->enumRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($enum->getDocumentor())) {
            return false;
        }

        foreach ($enum->getOptions() as $option) {
            $this->enumOptionRepo->delete($option);
        }
        $this->enumRepo->delete($enum);
        return true;
    }
}
