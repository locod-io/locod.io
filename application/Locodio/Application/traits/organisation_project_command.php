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

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\Organisation\AddProject\AddProject;
use App\Locodio\Application\Command\Organisation\AddProject\AddProjectHandler;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProject;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProjectHandler;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProject;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProjectHandler;
use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadDocumentorImage;
use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadDocumentImageHandler;
use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadProjectLogo;
use App\Locodio\Application\Command\Organisation\UploadProjectLogo\UploadProjectLogoHandler;
use App\Lodocio\Application\Command\Project\AddDocProject\AddDocProject;
use App\Lodocio\Application\Command\Project\AddDocProject\AddDocProjectHandler;
use App\Lodocio\Application\Command\Project\ChangeDocProject\ChangeDocProject;
use App\Lodocio\Application\Command\Project\ChangeDocProject\ChangeDocProjectHandler;

trait organisation_project_command
{
    public function addProject(\stdClass $jsonCommand): bool
    {
        $command = AddProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckOrganisationId($command->getOrganisationId());

        $handler = new AddProjectHandler($this->organisationRepository, $this->projectRepository);
        $projectUuid = $handler->go($command);
        $this->entityManager->flush();

        // also create a doc-project for further reference in the lodocio project
        $project = $this->projectRepository->getByUuid($projectUuid);
        $docProjectCommand = new AddDocProject(
            $command->getOrganisationId(),
            $project->getId(),
            $command->getName()
        );
        $docHandler = new AddDocProjectHandler($this->organisationRepository, $this->projectRepository, $this->docProjectRepository);
        $docHandler->go($docProjectCommand);

        $this->entityManager->flush();

        return true;
    }

    public function changeProject(\stdClass $jsonCommand): bool
    {
        $command = ChangeProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckProjectId($command->getId());

        $handler = new ChangeProjectHandler($this->projectRepository);
        $result = $handler->go($command);

        // also update the related doc-project
        $docProjectCommand = new ChangeDocProject(
            $command->getId(),
            $command->getName(),
            $command->getCode(),
            $command->getColor()
        );
        $docHandler = new ChangeDocProjectHandler($this->projectRepository, $this->docProjectRepository);
        $docResult = $docHandler->go($docProjectCommand);

        $this->entityManager->flush();
        return $result;
    }

    public function orderProjects(\stdClass $jsonCommand): bool
    {
        $command = OrderProject::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckProjectIds($command->getSequence());

        $handler = new OrderProjectHandler($this->projectRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function uploadLogoForProject(UploadProjectLogo $command): bool
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckUserId($command->getUserId());
        $this->permission->CheckProjectId($command->getProjectId());

        $handler = new UploadProjectLogoHandler(
            $this->userRepository,
            $this->projectRepository
        );

        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
