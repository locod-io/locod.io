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

namespace App\Lodocio\Application\Command\Wiki\UploadWikiFile;

trait UploadWikiFileTrait
{
    public function uploadImageForWikiNode(UploadWikiFile $command): bool
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckWikiNodeId($command->getWikiNodeId());

        $handler = new UploadWikiFileHandler(
            $this->wikiNodeRepository,
            $this->wikiNodeFileRepository,
        );

        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
