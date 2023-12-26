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

namespace App\Lodocio\Application\Command\Wiki\AddProjectDocument;

use App\Linear\Application\Command\DocumentMutation;
use App\Linear\Application\Command\DocumentSubject;
use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\Command\Wiki\ProjectDocumentType;
use App\Lodocio\Domain\Model\Wiki\WikiRelatedProjectDocumentRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use App\Lodocio\Infrastructure\Database\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Infrastructure\Database\Wiki\WikiNodeRepository;
use Doctrine\ORM\EntityNotFoundException;

class AddProjectDocumentHandler
{
    public function __construct(
        protected WikiRepository                       $wikiRepository,
        protected WikiNodeRepository                   $wikiNodeRepository,
        protected WikiNodeGroupRepository              $wikiNodeGroupRepository,
        protected WikiRelatedProjectDocumentRepository $documentRepository,
        protected LinearConfig                         $linearConfig,
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function go(AddProjectDocument $command): bool
    {
        switch ($command->getType()) {
            case ProjectDocumentType::WIKI:
                $wiki = $this->wikiRepository->getById($command->getSubjectId());
                if (strlen($wiki->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($wiki->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $projectDocument = $documentMutation->createDocument(
                    $command->getRelatedProjectId(),
                    $wiki->getName(),
                    $command->getContent(),
                    DocumentSubject::WIKI
                );
                if (!is_null($wiki->getRelatedProjectDocument())) {
                    $document = $wiki->getRelatedProjectDocument();
                    $document->change(
                        $projectDocument->getRelatedProjectId(),
                        $projectDocument->getRelatedDocumentId(),
                        $projectDocument->getTitle()
                    );
                    $this->documentRepository->save($document);
                } else {
                    $this->documentRepository->save($projectDocument);
                    $wiki->setRelatedProjectDocument($projectDocument);
                }
                $this->wikiRepository->save($wiki);
                break;
            case ProjectDocumentType::GROUP:
                $group = $this->wikiNodeGroupRepository->getById($command->getSubjectId());
                if (strlen($group->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($group->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $projectDocument = $documentMutation->createDocument(
                    $command->getRelatedProjectId(),
                    $group->getName(),
                    $command->getContent(),
                    DocumentSubject::WIKI
                );
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
                $this->wikiNodeGroupRepository->save($group);
                break;
            case ProjectDocumentType::NODE:
                $node = $this->wikiNodeRepository->getById($command->getSubjectId());
                if (strlen($node->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($node->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $projectDocument = $documentMutation->createDocument(
                    $command->getRelatedProjectId(),
                    $node->getName(),
                    $command->getContent(),
                    DocumentSubject::WIKI
                );
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
                $this->wikiNodeRepository->save($node);
                break;
        }

        return true;
    }
}
