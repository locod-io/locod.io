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

namespace App\Lodocio\Application\Command\Wiki\DeleteProjectDocument;

use App\Linear\Application\Command\DocumentMutation;
use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Application\Command\Wiki\ProjectDocumentType;
use App\Lodocio\Domain\Model\Wiki\WikiRelatedProjectDocumentRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use App\Lodocio\Infrastructure\Database\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Infrastructure\Database\Wiki\WikiNodeRepository;

class DeleteProjectDocumentHandler
{
    public function __construct(
        protected WikiRepository                       $wikiRepository,
        protected WikiNodeRepository                   $wikiNodeRepository,
        protected WikiNodeGroupRepository              $wikiNodeGroupRepository,
        protected WikiRelatedProjectDocumentRepository $documentRepository,
        protected LinearConfig                         $linearConfig,
    ) {
    }

    public function go(DeleteProjectDocument $command): bool
    {
        switch ($command->getType()) {
            case ProjectDocumentType::WIKI:
                $wiki = $this->wikiRepository->getById($command->getSubjectId());
                if (strlen($wiki->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($wiki->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $resultMutation = $documentMutation->deleteDocument($command->getRelatedDocumentId());
                if (!is_null($wiki->getRelatedProjectDocument())) {
                    $relatedProjectDocument = $wiki->getRelatedProjectDocument();
                    $this->documentRepository->delete($relatedProjectDocument);
                    $wiki->setRelatedProjectDocument(null);
                    $this->wikiRepository->save($wiki);
                }
                break;
            case ProjectDocumentType::GROUP:
                $group = $this->wikiNodeGroupRepository->getById($command->getSubjectId());
                if (strlen($group->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($group->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $resultMutation = $documentMutation->deleteDocument($command->getRelatedDocumentId());
                if (!is_null($group->getRelatedProjectDocument())) {
                    $relatedProjectDocument = $group->getRelatedProjectDocument();
                    $this->documentRepository->delete($relatedProjectDocument);
                    $group->setRelatedProjectDocument(null);
                    $this->wikiNodeGroupRepository->save($group);
                }
                break;
            case ProjectDocumentType::NODE:
                $node = $this->wikiNodeRepository->getById($command->getSubjectId());
                if (strlen($node->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey()) !== 0) {
                    $this->linearConfig->setKey($node->getWiki()->getProject()->getProject()->getOrganisation()->getLinearApiKey());
                }
                $documentMutation = new DocumentMutation($this->linearConfig);
                $resultMutation = $documentMutation->deleteDocument($command->getRelatedDocumentId());
                if (!is_null($node->getRelatedProjectDocument())) {
                    $relatedProjectDocument = $node->getRelatedProjectDocument();
                    $this->documentRepository->delete($relatedProjectDocument);
                    $node->setRelatedProjectDocument(null);
                    $this->wikiNodeRepository->save($node);
                }
                break;
        }

        return true;
    }
}
