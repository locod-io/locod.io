<?php

namespace App\Locodio\Application\Command\Model\DeleteAttribute;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
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
        $attribute = $this->attributeRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($attribute->getDomainModel()->getDocumentor())) {
            return false;
        }

        $this->attributeRepo->delete($attribute);

        return true;
    }
}
