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

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;

class AddDocProjectHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected OrganisationRepository $organisationRepo,
        protected ProjectRepository      $projectRepo,
        protected DocProjectRepository   $docProjectRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddDocProject $command): bool
    {
        $organisation = $this->organisationRepo->getById($command->getOrganisationId());
        $project = $this->projectRepo->getById($command->getProjectId());

        $docProject = DocProject::make(
            $this->projectRepo->nextIdentity(),
            $command->getName(),
            strtoupper(substr($command->getName(), 0, 3)),
            $organisation,
            $project
        );

        $this->docProjectRepo->save($docProject);

        return true;
    }

}
