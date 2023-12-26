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

namespace App\Lodocio\Application\Command\Wiki\AddWiki;

trait AddWikiTrait
{
    /**
     * @throws \Exception
     */
    public function addWiki(\stdClass $jsonCommand): bool
    {
        $command = AddWiki::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckDocProjectId($command->getDocProjectId());

        $handler = new AddWikiHandler(
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
