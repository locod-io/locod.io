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

namespace App\Lodocio\Application\Command\Wiki\DeleteWiki;

trait DeleteWikiTrait
{
    /**
     * @throws \Exception
     */
    public function deleteWiki(\stdClass $jsonCommand): bool
    {
        $command = DeleteWiki::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckWikiId($command->getId());

        $handler = new DeleteWikiHandler(
            $this->wikiRepository,
            $this->wikiNodeRepository,
            $this->wikiNodeGroupRepository,
            $this->wikiNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
