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

namespace App\Lodocio\Application\Command\Wiki\AddWikiNodeStatus;

trait AddWikiNodeStatusTrait
{
    /**
     * @throws \Exception
     */
    public function addWikiNodeStatus(\stdClass $jsonCommand): bool
    {
        $command = AddWikiNodeStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckWikiId($command->getWikiId());

        $handler = new AddWikiNodeStatusHandler(
            $this->wikiRepository,
            $this->wikiNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
