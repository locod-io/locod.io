<?php

namespace App\Locodio\Application\Command\Model\DeleteEnumOption;

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
        $this->enumOptionRepo->delete($enumOption);
        return true;
    }
}
