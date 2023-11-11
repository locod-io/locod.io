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

namespace App\Tests\integration\application\Locodio\Application\Command\Organisation;

use App\Locodio\Application\Command\Organisation\AddProject\AddProject;
use App\Locodio\Application\Command\Organisation\AddProject\AddProjectHandler;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProject;
use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProjectHandler;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProject;
use App\Locodio\Application\Command\Organisation\OrderProject\OrderProjectHandler;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeAndOrderProjectTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateProjects(): array
    {
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $organisation = $modelFactory->makeOrganisation(Uuid::fromString('deb4a7a4-6339-4d33-809b-f3d048b44ca4'));

        $jsonCommand = new \stdClass();
        $jsonCommand->organisationId = $organisation->getId();
        $jsonCommand->name = 'project name';
        $command = AddProject::hydrateFromJson($jsonCommand);
        $commandHandler = new AddProjectHandler(
            $this->entityManager->getRepository(Organisation::class),
            $this->entityManager->getRepository(Project::class)
        );
        // -- make three projects
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- get the resulting projects
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $projects = $projectRepo->getByOrganisation($organisation);
        Assert::assertCount(3, $projects);
        return $projects;
    }

    /** @depends testCreateProjects */
    public function testChangeProject(array $projects): array
    {
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var Project $firstProject */
        $firstProject = $projects[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstProject->getId();
        $jsonCommand->name = 'changed project name';
        $jsonCommand->code = 'PRO';
        $jsonCommand->color = 'color';
        $jsonCommand->slug = 'some-slug';
        $jsonCommand->domainLayer = 'domainLayer';
        $jsonCommand->applicationLayer = 'applicationLayer';
        $jsonCommand->infrastructureLayer = 'infrastructureLayer';
        $command = ChangeProject::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeProjectHandler($projectRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the change project via the readmodel
        $project = $projectRepo->getById($firstProject->getId());
        $projectRM = ProjectRM::hydrateFromModel($project);
        $result = json_decode(json_encode($projectRM));
        Assert::assertEquals('changed project name', $result->name);
        Assert::assertEquals('PRO', $result->code);
        Assert::assertEquals('some-slug', $result->slug);
        Assert::assertEquals('#color', $result->color);
        Assert::assertEquals('domainLayer', $result->domainLayer);
        Assert::assertEquals('applicationLayer', $result->applicationLayer);
        Assert::assertEquals('infrastructureLayer', $result->infrastructureLayer);

        // -- get the resulting projects
        $projects = $projectRepo->getByOrganisation($firstProject->getOrganisation());
        Assert::assertCount(3, $projects);
        return $projects;
    }

    /** @depends testChangeProject */
    public function testOrderProject(array $projects): void
    {
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $currentOrder = [];
        foreach ($projects as $project) {
            $currentOrder[] = $project->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderProject::hydrateFromJson($jsonCommand);
        $commandHandler = new OrderProjectHandler($projectRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $projects = $projectRepo->getByOrganisation($projects[0]->getOrganisation());
        Assert::assertCount(3, $projects);
        $resultOrder = [];
        foreach ($projects as $project) {
            $resultOrder[] = $project->getId();
        }
        Assert::assertEquals($resultOrder, $newOrder);
    }
}
