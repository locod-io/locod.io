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

namespace App\Lodocio\Application\Command\Tracker\ChangeTrackerNodeStatus;

trait ChangeTrackerNodeStatusTrait
{
    /**
     * @throws \Exception
     */
    public function changeTrackerNodeStatus(\stdClass $jsonCommand): bool
    {
        $command = ChangeTrackerNodeStatus::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeStatusId($command->getId());

        $handler = new ChangeTrackerNodeStatusHandler(
            $this->trackerNodeStatusRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
