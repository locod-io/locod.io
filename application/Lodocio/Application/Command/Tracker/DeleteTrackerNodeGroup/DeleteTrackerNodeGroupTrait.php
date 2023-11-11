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

namespace App\Lodocio\Application\Command\Tracker\DeleteTrackerNodeGroup;

trait DeleteTrackerNodeGroupTrait
{
    /**
     * @throws \Exception
     */
    public function deleteTrackerNodeGroup(\stdClass $jsonCommand): bool
    {
        $command = DeleteTrackerNodeGroup::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeGroupId($command->getId());
        $handler = new DeleteTrackerNodeGroupHandler(
            $this->trackerRepository,
            $this->trackerNodeRepository,
            $this->trackerNodeGroupRepository,
        );
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
