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

namespace App\Locodio\Application\Command\Organisation\AddProject;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddProjectHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected OrganisationRepository $organisationRepo,
        protected ProjectRepository      $projectRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddProject $command): bool
    {
        $organisation = $this->organisationRepo->getById($command->getOrganisationId());
        $project = Project::make(
            $this->projectRepo->nextIdentity(),
            $command->getName(),
            strtoupper(substr($command->getName(), 0, 3)),
            $organisation
        );

        // -- set default layer names

        $packageName = $this->toCamelCase($command->getName(), true);
        $project->setLayers(
            "App\\" . $packageName . "\\Domain",
            "App\\" . $packageName . "\\Application",
            "App\\" . $packageName . "\\Infrastructure"
        );

        $this->projectRepo->save($project);

        return true;
    }

    private function toCamelCase(string $string, bool $capitalizeFirstChar = false): string
    {
        $str = str_replace(str_split(' -'), '', ucwords($string, ' -'));
        if (!$capitalizeFirstChar) {
            $str = lcfirst($str);
        }
        return $str;
    }
}
