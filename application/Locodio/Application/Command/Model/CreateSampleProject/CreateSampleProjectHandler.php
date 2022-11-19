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

namespace App\Locodio\Application\Command\Model\CreateSampleProject;

use App\Locodio\Application\Command\Organisation\ImportProject\ImportProject;
use App\Locodio\Application\Command\Organisation\ImportProject\ImportProjectHandler;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\AssociationRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class CreateSampleProjectHandler
{
    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected AttributeRepository   $fieldRepo,
        protected AssociationRepository $relationRepo,
        protected EnumRepository        $enumRepo,
        protected EnumOptionRepository  $enumOptionRepo,
        protected QueryRepository       $queryRepo,
        protected CommandRepository     $commandRepo,
        protected TemplateRepository    $templateRepo
    ) {
    }

    public function go(CreateSampleProject $command): bool
    {
        $jsonFile = __DIR__ . '/ExampleBookProject.json';
        $jsonString = file_get_contents($jsonFile);
        $projectClass = json_decode($jsonString);
        $command = new ImportProject($command->getProjectUuid(), $projectClass);
        $importHandler = new ImportProjectHandler(
            $this->projectRepo,
            $this->domainModelRepo,
            $this->fieldRepo,
            $this->relationRepo,
            $this->enumRepo,
            $this->enumOptionRepo,
            $this->queryRepo,
            $this->commandRepo,
            $this->templateRepo
        );
        return $importHandler->go($command);
    }
}
