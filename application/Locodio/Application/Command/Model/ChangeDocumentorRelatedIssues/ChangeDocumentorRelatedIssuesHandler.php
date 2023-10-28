<?php

namespace App\Locodio\Application\Command\Model\ChangeDocumentorRelatedIssues;

use App\Locodio\Domain\Model\Model\DocumentorRepository;

class ChangeDocumentorRelatedIssuesHandler
{
    public function __construct(
        protected DocumentorRepository $documentorRepo,
    ) {
    }

    public function go(ChangeDocumentorRelatedIssues $command): bool
    {
        $model = $this->documentorRepo->getById($command->getId());
        $model->setLinearIssues($command->getRelatedIssues());
        $this->documentorRepo->save($model);
        return true;
    }

}
