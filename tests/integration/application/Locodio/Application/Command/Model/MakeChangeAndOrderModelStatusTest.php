<?php

namespace App\Tests\integration\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\AddModelStatus\AddModelStatus;
use App\Locodio\Application\Command\Model\AddModelStatus\AddModelStatusHandler;
use App\Locodio\Application\Command\Model\ChangeModelStatus\ChangeModelStatus;
use App\Locodio\Application\Command\Model\ChangeModelStatus\ChangeModelStatusHandler;
use App\Locodio\Application\Command\Model\OrderModelStatus\OrderModelStatus;
use App\Locodio\Application\Command\Model\OrderModelStatus\OrderModelStatusHandler;
use App\Locodio\Application\Command\Model\SaveModelStatusWorkflow\SaveModelStatusWorkflow;
use App\Locodio\Application\Command\Model\SaveModelStatusWorkflow\SaveModelStatusWorkflowHandler;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRM;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeAndOrderModelStatusTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeModelStatus(): array
    {
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $modelStatusRepo = $this->entityManager->getRepository(ModelStatus::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $modelFactory->makeProject(Uuid::fromString('9ae8c6bf-bc24-4cbe-8ffe-e8533122c60c'));

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->name = "name";
        $jsonCommand->color = "color";
        $jsonCommand->isStart = true;
        $jsonCommand->isFinal = true;

        $command = AddModelStatus::hydrateFromJson($jsonCommand);
        $commandHandler = new AddModelStatusHandler($projectRepo, $modelStatusRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $modelStatus = $modelStatusRepo->getByProject($project);
        Assert::assertCount(3, $modelStatus);
        $countFinal = 0;
        $countStart = 0;
        foreach ($modelStatus as $status) {
            if ($status->isFinal()) {
                $countFinal = $countFinal+1;
            }
            if ($status->isStart()) {
                $countStart = $countStart+1;
            }
        }
        Assert::assertEquals(1, $countStart);
        Assert::assertEquals(1, $countFinal);
        return $modelStatus;
    }

    /** @depends testMakeModelStatus */
    public function testChangeModelStatus(array $modelStatus): array
    {
        $modelStatusRepo = $this->entityManager->getRepository(ModelStatus::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var ModelStatus $firstDomainModel */
        $firstModelStatus = $modelStatus[0];

        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstModelStatus->getId();
        $jsonCommand->name = "changed name";
        $jsonCommand->color = "changed color";
        $jsonCommand->isFinal = true;
        $jsonCommand->isStart = true;

        $command = ChangeModelStatus::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeModelStatusHandler($modelStatusRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $status = $modelStatusRepo->getById($firstModelStatus->getId());
        $statusRM = ModelStatusRM::hydrateFromModel($status, true);
        $result = json_decode(json_encode($statusRM));
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals('changed color', $result->color);
        Assert::assertEquals(true, $result->isStart);
        Assert::assertEquals(true, $result->isFinal);

        // -- save the workflow and test the workflow

        $jsonCommand = new \stdClass();
        $jsonCommand->workflow = [];
        $item = new \stdClass();
        $item->id = strval($firstModelStatus->getId());
        $item->label = 'label';
        $position = new \stdClass();
        $position->x = 10;
        $position->y = 20;
        $item->position = $position;
        $item->flowIn = [1, 2];
        $item->flowOut = [3, 4];
        $jsonCommand->workflow[] = $item;
        $commandSave = SaveModelStatusWorkflow::hydrateFromJson($jsonCommand);
        $saveCommandHandler = new SaveModelStatusWorkflowHandler($modelStatusRepo);
        $saveCommandHandler->go($commandSave);
        $this->entityManager->flush();

        $status = $modelStatusRepo->getById($firstModelStatus->getId());
        $statusRM = ModelStatusRM::hydrateFromModel($status, true);
        $result = json_decode(json_encode($statusRM));
        Assert::assertEquals(10, $result->x);
        Assert::assertEquals(20, $result->y);
        Assert::assertEquals([1, 2], $result->flowIn);
        Assert::assertEquals([3, 4], $result->flowOut);

        $project = $projectRepo->getByUuid(Uuid::fromString('9ae8c6bf-bc24-4cbe-8ffe-e8533122c60c'));
        $modelStatus = $modelStatusRepo->getByProject($project);

        $countFinal = 0;
        $countStart = 0;
        foreach ($modelStatus as $status) {
            if ($status->isFinal()) {
                $countFinal = $countFinal+1;
            }
            if ($status->isStart()) {
                $countStart = $countStart+1;
            }
        }
        Assert::assertEquals(1, $countStart);
        Assert::assertEquals(1, $countFinal);

        return $modelStatus;
    }

    /** @depends testChangeModelStatus */
    public function testOrderModelStatus(array $modelStatus): array
    {
        $modelStatusRepo = $this->entityManager->getRepository(ModelStatus::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $currentOrder = [];
        foreach ($modelStatus as $status) {
            $currentOrder[] = $status->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderModelStatus::hydrateFromJson($jsonCommand);
        $commandHandler = new OrderModelStatusHandler($modelStatusRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('9ae8c6bf-bc24-4cbe-8ffe-e8533122c60c'));
        $modelStatus = $modelStatusRepo->getByProject($project);
        $resultOrder = [];
        foreach ($modelStatus as $status) {
            $resultOrder[] = $status->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $modelStatus;
    }

    // todo test only one status can have status final
}
