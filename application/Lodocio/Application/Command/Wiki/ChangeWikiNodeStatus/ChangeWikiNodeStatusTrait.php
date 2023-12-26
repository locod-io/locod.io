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

namespace App\Lodocio\Application\Command\Wiki\ChangeWikiNodeStatus;

trait ChangeWikiNodeStatusTrait
{
    /**
     * @throws \Exception
     */
    public function changeWikiNodeStatus(\stdClass $jsonCommand): bool
    {
        $command = ChangeWikiNodeStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckWikiNodeStatusId($command->getId());

        $handler = new ChangeWikiNodeStatusHandler(
            $this->wikiNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
