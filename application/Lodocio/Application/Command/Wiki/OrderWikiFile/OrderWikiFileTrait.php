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

namespace App\Lodocio\Application\Command\Wiki\OrderWikiFile;

trait OrderWikiFileTrait
{
    public function orderWikiFile(\stdClass $jsonCommand): bool
    {
        $command = OrderWikiFile::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeFileIds($command->getSequence());

        $handler = new OrderWikiFileHandler(
            $this->wikiNodeFileRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();

        return $result;
    }
}
