<?php

namespace App\Locodio\Application\Command\Model\DeleteAttribute;

use App\Locodio\Domain\Model\Model\AttributeRepository;

class DeleteAttributeHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected AttributeRepository $attributeRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteAttribute $command): bool
    {
        $field = $this->attributeRepo->getById($command->getId());
        $this->attributeRepo->delete($field);
        return true;
    }
}
