<?php

namespace App\Locodio\Application\Command\Model\DeleteEnum;

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
        foreach ($enum->getOptions() as $option) {
            $this->enumOptionRepo->delete($option);
        }
        $this->enumRepo->delete($enum);
        return true;
    }
}
