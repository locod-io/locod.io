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

namespace App\Lodocio\Application\Command\Tracker\DeleteTrackerFile;

trait DeleteTrackerFileTrait
{
    /**
     * @throws \Exception
     */
    public function deleteTrackerFile(\stdClass $jsonCommand): bool
    {
        $command = DeleteTrackerFile::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeFileId($command->getId());

        $handler = new DeleteTrackerFileHandler(
            $this->trackerNodeFileRepository,
            $this->uploadFolder
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
