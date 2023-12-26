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

namespace App\Lodocio\Application\Command\Wiki\SyncWikiStructure;

trait SyncWikiStructureTrait
{
    /**
     * @throws \Exception
     */
    public function syncWikiStructure(\stdClass $jsonCommand): bool
    {
        $command = SyncWikiStructure::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($command->getId());

        $handler = new SyncWikiStructureHandler(
            $this->entityManager,
            $this->docProjectRepository,
            $this->wikiRepository,
            $this->wikiNodeStatusRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository
        );
        $result = $handler->go($command);
        $this->entityManager->flush();

        return $result;
    }

}
