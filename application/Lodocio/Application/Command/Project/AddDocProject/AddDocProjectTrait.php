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

namespace App\Lodocio\Application\Command\Project\AddDocProject;

trait AddDocProjectTrait
{
    public function addDocProject(\stdClass $jsonCommand): bool
    {
        $command = AddDocProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckDocProjectId($command->getProjectId());

        $handler = new AddDocProjectHandler(
            $this->organisationRepository,
            $this->projectRepository,
            $this->docProjectRepository
        );
        $result = $handler->go($command);

        $this->entityManager->flush();
        return $result;
    }
}
