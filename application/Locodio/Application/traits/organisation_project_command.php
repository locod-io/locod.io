<?php

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\Organisation\AddProject\AddProject;
use App\Locodio\Application\Command\Organisation\AddProject\AddProjectHandler;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProject;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProjectHandler;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProject;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProjectHandler;

trait organisation_project_command
{
    public function addProject(\stdClass $jsonCommand): bool
    {
        $command = AddProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckOrganisationId($command->getOrganisationId());

        $handler = new AddProjectHandler($this->organisationRepository, $this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeProject(\stdClass $jsonCommand): bool
    {
        $command = ChangeProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getId());

        $handler = new ChangeProjectHandler($this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderProjects(\stdClass $jsonCommand): bool
    {
        $command = OrderProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectIds($command->getSequence());

        $handler = new OrderProjectHandler($this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
