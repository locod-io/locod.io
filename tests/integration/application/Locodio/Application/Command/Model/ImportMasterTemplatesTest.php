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

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplates;
use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplatesHandler;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class ImportMasterTemplatesTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testImportTemplates(): void
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);

        $projectRepo = $this->entityManager->getRepository(Project::class);
        $masterTemplateRepo = $this->entityManager->getRepository(MasterTemplate::class);
        $templateRepo = $this->entityManager->getRepository(Template::class);

        // -- make some models
        $user = $modelFactory->makeUser('importTemplates@test.com');
        $masterTemplate1 = $modelFactory->makeMasterTemplate(
            $user,
            Uuid::fromString('3cafb45b-b04e-4109-8e1f-068ed8ebc8f6')
        );
        $masterTemplate2 = $modelFactory->makeMasterTemplate(
            $user,
            Uuid::fromString('0d7b2555-1b4e-4932-a8d8-9bfd10559165')
        );
        $project = $modelFactory->makeProject(Uuid::fromString('098ff21e-4d80-455c-adc6-65043d144427'));

        // -- import the templates
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->masterTemplateIds = [$masterTemplate1->getId(), $masterTemplate2->getId()];
        $command = ImportTemplatesFromMasterTemplates::hydrateFromJson($jsonCommand);
        $commandHandler = new ImportTemplatesFromMasterTemplatesHandler(
            $projectRepo,
            $masterTemplateRepo,
            $templateRepo
        );
        $commandHandler->go($command);
        $this->entityManager->flush();

        $templates = $templateRepo->getByProject($project);
        Assert::assertCount(2, $templates);
    }
}
