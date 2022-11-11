<?php

namespace App\Locodio\Application\Command\Model\DeleteField;

use App\Locodio\Domain\Model\Model\FieldRepository;

class DeleteFieldHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected FieldRepository $fieldRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteField $command): bool
    {
        $field = $this->fieldRepo->getById($command->getId());
        $this->fieldRepo->delete($field);
        return true;
    }
}
