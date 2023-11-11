<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Tracker\DeleteProjectDocument;

use App\Locodio\Application\Query\Linear\LinearConfig;
use App\Lodocio\Application\Command\Linear\DocumentMutation;
use App\Lodocio\Application\Command\Tracker\ProjectDocumentType;
use App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocumentRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeRepository;

class DeleteProjectDocumentHandler
{
    public function __construct(
        protected TrackerRepository                       $trackerRepository,
        protected TrackerNodeRepository                   $trackerNodeRepository,
        protected TrackerNodeGroupRepository              $trackerNodeGroupRepository,
        protected TrackerRelatedProjectDocumentRepository $documentRepository,
        protected LinearConfig                            $linearConfig,
    )
    {
    }

    public function go(DeleteProjectDocument $command): bool
    {
        switch ($command->getType()) {
            case ProjectDocumentType::TRACKER:
                $tracker = $this->trackerRepository->getById($command->getSubjectId());
                if (strlen($tracker->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($tracker->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $resultMutation = $documentMutation->deleteDocument($command->getRelatedDocumentId());
                if (!is_null($tracker->getRelatedProjectDocument())) {
                    $relatedProjectDocument = $tracker->getRelatedProjectDocument();
                    $this->documentRepository->delete($relatedProjectDocument);
                    $tracker->setRelatedProjectDocument(null);
                    $this->trackerRepository->save($tracker);
                }
                break;
            case ProjectDocumentType::GROUP:
                $group = $this->trackerNodeGroupRepository->getById($command->getSubjectId());
                if (strlen($group->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($group->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $resultMutation = $documentMutation->deleteDocument($command->getRelatedDocumentId());
                if (!is_null($group->getRelatedProjectDocument())) {
                    $relatedProjectDocument = $group->getRelatedProjectDocument();
                    $this->documentRepository->delete($relatedProjectDocument);
                    $group->setRelatedProjectDocument(null);
                    $this->trackerNodeGroupRepository->save($group);
                }
                break;
            case ProjectDocumentType::NODE:
                $node = $this->trackerNodeRepository->getById($command->getSubjectId());
                if (strlen($node->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($node->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $resultMutation = $documentMutation->deleteDocument($command->getRelatedDocumentId());
                if (!is_null($node->getRelatedProjectDocument())) {
                    $relatedProjectDocument = $node->getRelatedProjectDocument();
                    $this->documentRepository->delete($relatedProjectDocument);
                    $node->setRelatedProjectDocument(null);
                    $this->trackerNodeRepository->save($node);
                }
                break;
        }

        return true;
    }
}