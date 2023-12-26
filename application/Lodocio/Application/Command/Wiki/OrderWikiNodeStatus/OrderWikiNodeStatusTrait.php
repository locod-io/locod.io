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

namespace App\Lodocio\Application\Command\Wiki\OrderWikiNodeStatus;

trait OrderWikiNodeStatusTrait
{
    public function orderWikiNodeStatus(\stdClass $jsonCommand): bool
    {
        $command = OrderWikiNodeStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckWikiNodeStatusIds($command->getSequence());

        $handler = new OrderWikiNodeStatusHandler(
            $this->wikiNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
