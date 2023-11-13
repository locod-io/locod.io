<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\Query\Model;

use App\Linear\Application\Query\GetIssues;
use App\Linear\Application\Query\LinearConfig;
use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use App\Locodio\Application\Query\Model\Readmodel\DocumentorRM;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetDocumentor
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected DocumentorRepository   $documentorRepo,
        protected ModuleRepository       $moduleRepo,
        protected DomainModelRepository  $domainModelRepo,
        protected EnumRepository         $enumRepo,
        protected QueryRepository        $queryRepo,
        protected CommandRepository      $commandRepo,
        protected ModelStatusRepository  $modelStatusRepo,
        protected LinearConfig           $linearConfig
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

    public function ById(int $id): DocumentorRM
    {
        return DocumentorRM::hydrateFromModel($this->documentorRepo->getById($id), true);
    }

    public function ByTypeAndSubjectId(DocumentorType $type, int $subjectId): DocumentorRM
    {
        switch ($type) {
            case DocumentorType::DOMAIN_MODEL:
                $model = $this->domainModelRepo->getById($subjectId);
                $repo = $this->domainModelRepo;
                break;
            case DocumentorType::ENUM:
                $model = $this->enumRepo->getById($subjectId);
                $repo = $this->enumRepo;
                break;
            case DocumentorType::QUERY:
                $model = $this->queryRepo->getById($subjectId);
                $repo = $this->queryRepo;
                break;
            case DocumentorType::COMMAND:
                $model = $this->commandRepo->getById($subjectId);
                $repo = $this->commandRepo;
                break;
            default:
                $model = $this->moduleRepo->getById($subjectId);
                $repo = $this->moduleRepo;
                break;
        }

        if (true === is_null($model->getDocumentor())) {
            // -- get the new status
            $newStatus = $this->modelStatusRepo->getStartByProject($model->getProject());
            $uuid = $this->documentorRepo->nextIdentity();
            // -- make a documentor
            $documentor = Documentor::make($uuid, $type, '', $newStatus);
            $this->documentorRepo->save($documentor);
            $this->entityManager->flush();
            // -- document the model
            $documentorSaved = $this->documentorRepo->getByUuid($uuid);
            $model->document($documentorSaved);
            $repo->save($model);
            // -- return the new documentor
            return DocumentorRM::hydrateFromModel($documentorSaved, true);
        } else {
            return DocumentorRM::hydrateFromModel($model->getDocumentor(), true);
        }
    }

    public function RelatedIssues(int $id): IssueReadModelCollection
    {
        $documentor = $this->documentorRepo->getById($id);
        $collection = new IssueReadModelCollection();
        $model = match ($documentor->getType()) {
            DocumentorType::DOMAIN_MODEL => $this->domainModelRepo->getByDocumentor($documentor),
            DocumentorType::ENUM => $this->enumRepo->getByDocumentor($documentor),
            DocumentorType::QUERY => $this->queryRepo->getByDocumentor($documentor),
            DocumentorType::COMMAND => $this->commandRepo->getByDocumentor($documentor),
            default => $this->moduleRepo->getByDocumentor($documentor),
        };
        if (strlen($model->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($model->getProject()->getOrganisation()->getLinearApiKey());
        }
        $getIssue = new GetIssues($this->linearConfig);
        foreach ($documentor->getLinearIssues() as $linearIssue) {
            $issue = $getIssue->ByIssueId($linearIssue['id']);
            $collection->addItem($issue);
        }
        return $collection;
    }

}
