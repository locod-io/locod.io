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

use App\Locodio\Application\Command\Model\AddEnum\AddEnum;
use App\Locodio\Application\Command\Model\AddEnum\AddEnumHandler;
use App\Locodio\Application\Command\Model\ChangeEnum\ChangeEnum;
use App\Locodio\Application\Command\Model\ChangeEnum\ChangeEnumHandler;
use App\Locodio\Application\Command\Model\DeleteEnum\DeleteEnum;
use App\Locodio\Application\Command\Model\DeleteEnum\DeleteEnumHandler;
use App\Locodio\Application\Command\Model\OrderEnum\OrderEnum;
use App\Locodio\Application\Command\Model\OrderEnum\OrderEnumHandler;
use App\Locodio\Application\Query\Model\Readmodel\EnumRM;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteEnumTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeEnums(): array
    {
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $domainModel = $modelFactory->makeDomainModel(Uuid::fromString('e5c8b103-bb87-4a25-9f22-0815be64742c'));
        $project = $domainModel->getProject();

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->domainModelId = $domainModel->getId();
        $jsonCommand->name = "command";
        $command = AddEnum::hydrateFromJson($jsonCommand);
        $commandHandler = new AddEnumHandler($projectRepo, $domainModelRepo, $enumRepo, $enumOptionRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $queries = $enumRepo->getByProject($project);
        Assert::assertCount(3, $queries);

        return $queries;
    }

    /** @depends testMakeEnums */
    public function testChangeEnums(array $enums): array
    {
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var Enum $firstEnum */
        $firstEnum = $enums[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstEnum->getId();
        $jsonCommand->domainModelId = $firstEnum->getDomainModel()->getId();
        $jsonCommand->name = "changed name";
        $jsonCommand->namespace = "namespace";
        $command = ChangeEnum::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeEnumHandler($domainModelRepo, $enumRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the changes via the readmodel
        $enum = $enumRepo->getById($firstEnum->getId());
        $enumRM = EnumRM::hydrateFromModel($enum);
        $result = json_decode(json_encode($enumRM));
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals('namespace', $result->namespace);
        Assert::assertCount(1, $result->options);

        return $enums;
    }

    /** @depends testChangeEnums */
    public function testOrderEnums(array $enums): array
    {
        $currentOrder = [];
        foreach ($enums as $enum) {
            $currentOrder[] = $enum->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderEnum::hydrateFromJson($jsonCommand);
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $commandHandler = new OrderEnumHandler($enumRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString('e5c8b103-bb87-4a25-9f22-0815be64742c'));
        $enums = $enumRepo->getByProject($project);
        $resultOrder = [];
        foreach ($enums as $enum) {
            $resultOrder[] = $enum->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $enums;
    }

    /** @depends testOrderEnums */
    public function testDeleteEnum(array $enums): void
    {
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);

        /** @var Enum $firstEnum */
        $firstEnum = $enums[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstEnum->getId();
        $command = DeleteEnum::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteEnumHandler($enumRepo, $enumOptionRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('e5c8b103-bb87-4a25-9f22-0815be64742c'));
        $enums = $enumRepo->getByProject($project);
        Assert::assertCount(2, $enums);
    }
}
