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

namespace App\Lodocio\Application\Command\Wiki\DeleteWikiFile;

trait DeleteWikiFileTrait
{
    /**
     * @throws \Exception
     */
    public function deleteWikiFile(\stdClass $jsonCommand): bool
    {
        $command = DeleteWikiFile::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeFileId($command->getId());

        $handler = new DeleteWikiFileHandler(
            $this->wikiNodeFileRepository,
            $this->uploadFolder
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
