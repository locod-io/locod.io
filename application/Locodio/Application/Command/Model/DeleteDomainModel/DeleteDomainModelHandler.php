<?php

namespace App\Locodio\Application\Command\Model\DeleteDomainModel;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\AssociationRepository;

class DeleteDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected AttributeRepository   $attributeRepo,
        protected AssociationRepository $associationRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteDomainModel $command): bool
    {
        $domainModel = $this->domainModelRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($domainModel->getDocumentor())) {
            return false;
        }

        foreach ($domainModel->getAttributes() as $attribute) {
            $this->attributeRepo->delete($attribute);
        }
        foreach ($domainModel->getAssociations() as $association) {
            $this->associationRepo->delete($association);
        }
        $this->domainModelRepo->delete($domainModel);

        return true;
    }
}
