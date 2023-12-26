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

namespace App\Lodocio\Application\Command\Tracker\ChangeNodeName;

trait ChangeNodeNameTrait
{
    /**
     * @throws \Exception
     */
    public function changeNodeName(\stdClass $jsonCommand): bool
    {
        $command = ChangeNodeName::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckTrackerNodeId($command->getId());
        $handler = new ChangeNodeNameHandler($this->trackerNodeRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
