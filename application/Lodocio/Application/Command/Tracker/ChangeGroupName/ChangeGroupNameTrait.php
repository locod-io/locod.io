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

namespace App\Lodocio\Application\Command\Tracker\ChangeGroupName;

trait ChangeGroupNameTrait
{
    /**
     * @throws \Exception
     */
    public function changeGroupName(\stdClass $jsonCommand): bool
    {
        $command = ChangeGroupName::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeGroupId($command->getId());
        $handler = new ChangeGroupNameHandler($this->trackerNodeGroupRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
