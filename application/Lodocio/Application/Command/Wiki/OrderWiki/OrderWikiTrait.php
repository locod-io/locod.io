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

namespace App\Lodocio\Application\Command\Wiki\OrderWiki;

trait OrderWikiTrait
{
    public function orderWikis(\stdClass $jsonCommand): bool
    {
        $command = OrderWiki::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckWikisId($command->getSequence());

        $handler = new OrderWikiHandler(
            $this->wikiRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
