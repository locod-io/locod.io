<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\Command\Organisation\ChangeRelatedRoadmaps;

trait ChangeRelatedRoadmapsTrait
{
    /**
     * @throws \Exception
     */
    public function changeProjectRelatedRoadmaps(\stdClass $jsonCommand): bool
    {
        $command = ChangeRelatedRoadmaps::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getId());

        $handler = new ChangeRelatedRoadmapsHandler($this->projectRepository);
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }

}
