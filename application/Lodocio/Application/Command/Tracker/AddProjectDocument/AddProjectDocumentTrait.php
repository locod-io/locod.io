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

use App\Lodocio\Application\Command\Tracker\ProjectDocumentType;

trait AddProjectDocumentTrait
{
    /**
     * @throws \Exception
     */
    public function addProjectDocument(\stdClass $jsonCommand): bool
    {
        $command = AddProjectDocument::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        switch ($command->getType()) {
            case ProjectDocumentType::TRACKER:
                $this->permission->CheckTrackerId($command->getSubjectId());
                break;
            case ProjectDocumentType::GROUP:
                $this->permission->CheckTrackerNodeGroupId($command->getSubjectId());
                break;
            case ProjectDocumentType::NODE:
                $this->permission->CheckTrackerNodeId($command->getSubjectId());
                break;
        }

        $handler = new AddProjectDocumentHandler(
            $this->trackerRepository,
            $this->trackerNodeRepository,
            $this->trackerNodeGroupRepository,
            $this->documentRepository,
            $this->linearConfig,
        );

        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
