<?php

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\AddModule\AddModule;
use App\Locodio\Application\Command\Model\AddModule\AddModuleHandler;
use App\Locodio\Application\Command\Model\ChangeModule\ChangeModule;
use App\Locodio\Application\Command\Model\ChangeModule\ChangeModuleHandler;
use App\Locodio\Application\Command\Model\OrderModule\OrderModule;
use App\Locodio\Application\Command\Model\OrderModule\OrderModuleHandler;
use App\Locodio\Application\Query\Model\Readmodel\ModuleRM;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeAndOrderModuleTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeModules(): array
    {
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $moduleRepo = $this->entityManager->getRepository(Module::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $modelFactory->makeProject(Uuid::fromString('b2374728-d9b2-4c30-a17a-a8f12929aae2'));

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->name = "name";
        $jsonCommand->namespace = "namespace";
        $command = AddModule::hydrateFromJson($jsonCommand);
        $commandHandler = new AddModuleHandler($projectRepo, $moduleRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $modules = $moduleRepo->getByProject($project);
        Assert::assertCount(3, $modules);
        return $modules;
    }

    /** @depends testMakeModules */
    public function testChangeModule(array $modules): array
    {
        $moduleRepo = $this->entityManager->getRepository(Module::class);

        /** @var Module $firstModule */
        $firstModule = $modules[0];

        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstModule->getId();
        $jsonCommand->name = "changed name";
        $jsonCommand->namespace = "changed namespace";

        $command = ChangeModule::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeModuleHandler($moduleRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $module = $moduleRepo->getById($firstModule->getId());
        $moduleRM = ModuleRM::hydrateFromModel($module);
        $result = json_decode(json_encode($moduleRM));
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals('changed namespace', $result->namespace);

        return $modules;
    }

    /** @depends testChangeModule */
    public function testOrderModules(array $modules): void
    {
        $moduleRepo = $this->entityManager->getRepository(Module::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $currentOrder = [];
        foreach ($modules as $module) {
            $currentOrder[] = $module->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderModule::hydrateFromJson($jsonCommand);
        $commandHandler = new OrderModuleHandler($moduleRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('b2374728-d9b2-4c30-a17a-a8f12929aae2'));
        $modules = $moduleRepo->getByProject($project);
        $resultOrder = [];
        foreach ($modules as $module) {
            $resultOrder[] = $module->getId();
        }

        Assert::assertEquals($newOrder, $resultOrder);
    }
}
