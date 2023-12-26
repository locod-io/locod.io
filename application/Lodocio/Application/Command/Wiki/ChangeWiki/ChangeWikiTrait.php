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

namespace App\Lodocio\Application\Command\Wiki\ChangeWiki;

trait ChangeWikiTrait
{
    public function changeWiki(\stdClass $jsonCommand): bool
    {
        $command = ChangeWiki::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiId($command->getId());

        $handler = new ChangeWikiHandler(
            $this->wikiRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
