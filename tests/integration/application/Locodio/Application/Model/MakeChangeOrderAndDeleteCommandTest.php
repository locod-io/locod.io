<?php

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\AddCommand\AddCommand;
use App\Locodio\Application\Command\Model\AddCommand\AddCommandHandler;
use App\Locodio\Application\Command\Model\ChangeCommand\ChangeCommand;
use App\Locodio\Application\Command\Model\ChangeCommand\ChangeCommandHandler;
use App\Locodio\Application\Command\Model\DeleteCommand\DeleteCommand;
use App\Locodio\Application\Command\Model\DeleteCommand\DeleteCommandHandler;
use App\Locodio\Application\Command\Model\OrderCommand\OrderCommand;
use App\Locodio\Application\Command\Model\OrderCommand\OrderCommandHandler;
use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteCommandTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeCommands(): array
    {
        $commandRepo = $this->entityManager->getRepository(Command::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $domainModel = $modelFactory->makeDomainModel(Uuid::fromString('2c38c52b-88cb-4d1d-a995-88b0816c1440'));
        $project = $domainModel->getProject();

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->domainModelId = $domainModel->getId();
        $jsonCommand->name = "command";
        $command = AddCommand::hydrateFromJson($jsonCommand);
        $commandHandler = new AddCommandHandler($projectRepo, $domainModelRepo, $commandRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $commands = $commandRepo->getByProject($project);
        Assert::assertCount(3, $commands);

        return $commands;
    }

    /** @depends testMakeCommands */
    public function testChangeCommand(array $commands): array
    {
        $commandRepo = $this->entityManager->getRepository(Command::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var Command $firstCommand */
        $firstCommand = $commands[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstCommand->getId();
        $jsonCommand->domainModelId = $firstCommand->getDomainModel()->getId();
        $jsonCommand->name = "changed name";
        $jsonCommand->namespace = "namespace";
        $jsonCommand->mapping = ["mapping1", "mapping2"];
        $command = ChangeCommand::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeCommandHandler($domainModelRepo, $commandRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the changes via the readmodel
        $commandResult = $commandRepo->getById($firstCommand->getId());
        $commandRM = CommandRM::hydrateFromModel($commandResult);
        $result = json_decode(json_encode($commandRM));
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals('namespace', $result->namespace);
        Assert::assertEquals(["mapping1", "mapping2"], $result->mapping);

        return $commands;
    }

    /** @depends testChangeCommand */
    public function testOrderCommands(array $commands): array
    {
        $currentOrder = [];
        foreach ($commands as $command) {
            $currentOrder[] = $command->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderCommand::hydrateFromJson($jsonCommand);
        $commandRepo = $this->entityManager->getRepository(Command::class);
        $commandHandler = new OrderCommandHandler($commandRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString('2c38c52b-88cb-4d1d-a995-88b0816c1440'));
        $commands = $commandRepo->getByProject($project);
        $resultOrder = [];
        foreach ($commands as $command) {
            $resultOrder[] = $command->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $commands;
    }

    /** @depends testOrderCommands */
    public function testDeleteCommand(array $commands): void
    {
        $commandRepo = $this->entityManager->getRepository(Command::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var Command $firstTemplate */
        $firstTemplate = $commands[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstTemplate->getId();
        $command = DeleteCommand::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteCommandHandler($commandRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('2c38c52b-88cb-4d1d-a995-88b0816c1440'));
        $commands = $commandRepo->getByProject($project);
        Assert::assertCount(2, $commands);
    }
}
