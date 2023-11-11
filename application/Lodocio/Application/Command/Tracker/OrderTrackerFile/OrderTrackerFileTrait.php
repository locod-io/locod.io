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

namespace App\Lodocio\Application\Command\Tracker\OrderTrackerFile;

trait OrderTrackerFileTrait
{
    public function orderTrackerFile(\stdClass $jsonCommand): bool
    {
        $command = OrderTrackerFile::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeFileIds($command->getSequence());

        $handler = new OrderTrackerFileHandler(
            $this->trackerNodeFileRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();

        return $result;
    }
}
