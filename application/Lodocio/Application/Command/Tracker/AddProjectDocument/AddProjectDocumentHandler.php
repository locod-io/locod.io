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

namespace App\Lodocio\Application\Command\Tracker\AddProjectDocument;

use App\Linear\Application\Command\DocumentMutation;
use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\Command\Tracker\ProjectDocumentType;
use App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocumentRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeGroupRepository;
use App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeRepository;
use Doctrine\ORM\EntityNotFoundException;

class AddProjectDocumentHandler
{
    public function __construct(
        protected TrackerRepository                       $trackerRepository,
        protected TrackerNodeRepository                   $trackerNodeRepository,
        protected TrackerNodeGroupRepository              $trackerNodeGroupRepository,
        protected TrackerRelatedProjectDocumentRepository $documentRepository,
        protected LinearConfig                            $linearConfig,
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function go(AddProjectDocument $command): bool
    {
        switch ($command->getType()) {
            case ProjectDocumentType::TRACKER:
                $tracker = $this->trackerRepository->getById($command->getSubjectId());
                if (strlen($tracker->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($tracker->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $projectDocument = $documentMutation->createDocument($command->getRelatedProjectId(), $tracker->getName(), $command->getContent());
                if (!is_null($tracker->getRelatedProjectDocument())) {
                    $document = $tracker->getRelatedProjectDocument();
                    $document->change(
                        $projectDocument->getRelatedProjectId(),
                        $projectDocument->getRelatedDocumentId(),
                        $projectDocument->getTitle()
                    );
                    $this->documentRepository->save($document);
                } else {
                    $this->documentRepository->save($projectDocument);
                    $tracker->setRelatedProjectDocument($projectDocument);
                }
                $this->trackerRepository->save($tracker);
                break;
            case ProjectDocumentType::GROUP:
                $group = $this->trackerNodeGroupRepository->getById($command->getSubjectId());
                if (strlen($group->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($group->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $projectDocument = $documentMutation->createDocument($command->getRelatedProjectId(), $group->getName(), $command->getContent());
                if (!is_null($group->getRelatedProjectDocument())) {
                    $document = $group->getRelatedProjectDocument();
                    $document->change(
                        $projectDocument->getRelatedProjectId(),
                        $projectDocument->getRelatedDocumentId(),
                        $projectDocument->getTitle()
                    );
                    $this->documentRepository->save($document);
                } else {
                    $this->documentRepository->save($projectDocument);
                    $group->setRelatedProjectDocument($projectDocument);
                }
                $this->trackerNodeGroupRepository->save($group);
                break;
            case ProjectDocumentType::NODE:
                $node = $this->trackerNodeRepository->getById($command->getSubjectId());
                if (strlen($node->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($node->getTracker()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $projectDocument = $documentMutation->createDocument($command->getRelatedProjectId(), $node->getName(), $command->getContent());
                if (!is_null($node->getRelatedProjectDocument())) {
                    $document = $node->getRelatedProjectDocument();
                    $document->change(
                        $projectDocument->getRelatedProjectId(),
                        $projectDocument->getRelatedDocumentId(),
                        $projectDocument->getTitle()
                    );
                    $this->documentRepository->save($document);
                } else {
                    $this->documentRepository->save($projectDocument);
                    $node->setRelatedProjectDocument($projectDocument);
                }
                $this->trackerNodeRepository->save($node);
                break;
        }

        return true;
    }
}
