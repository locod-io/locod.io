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

use App\Lodocio\Application\Command\Wiki\ProjectDocumentType;

trait DeleteProjectDocumentTrait
{
    /**
     * @throws \Exception
     */
    public function deleteProjectDocument(\stdClass $jsonCommand): bool
    {
        $command = DeleteProjectDocument::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        switch ($command->getType()) {
            case ProjectDocumentType::WIKI:
                $this->permission->CheckWikiId($command->getSubjectId());
                break;
            case ProjectDocumentType::GROUP:
                $this->permission->CheckWikiNodeGroupId($command->getSubjectId());
                break;
            case ProjectDocumentType::NODE:
                $this->permission->CheckWikiNodeId($command->getSubjectId());
                break;
        }

        $handler = new DeleteProjectDocumentHandler(
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->documentRepository,
            $this->linearConfig,
        );

        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
