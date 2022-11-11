<?php

namespace App\Locodio\Application\Command\Model\DeleteDomainModel;

use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\FieldRepository;
use App\Locodio\Domain\Model\Model\RelationRepository;

class DeleteDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected FieldRepository       $fieldRepo,
        protected RelationRepository    $relationRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteDomainModel $command): bool
    {
        $domainModel = $this->domainModelRepo->getById($command->getId());
        foreach ($domainModel->getFields() as $field) {
            $this->fieldRepo->delete($field);
        }
        foreach ($domainModel->getRelations() as $relation) {
            $this->relationRepo->delete($relation);
        }
        $this->domainModelRepo->delete($domainModel);
        return true;
    }
}
