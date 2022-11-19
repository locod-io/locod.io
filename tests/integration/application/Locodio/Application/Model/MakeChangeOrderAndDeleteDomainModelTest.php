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

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModel;
use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModelHandler;
use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModel;
use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModelHandler;
use App\Locodio\Application\Command\Model\DeleteDomainModel\DeleteDomainModel;
use App\Locodio\Application\Command\Model\DeleteDomainModel\DeleteDomainModelHandler;
use App\Locodio\Application\Command\Model\OrderDomainModel\OrderDomainModel;
use App\Locodio\Application\Command\Model\OrderDomainModel\OrderDomainModelHandler;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteDomainModelTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeDomainModels(): array
    {
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $project = $modelFactory->makeProject(Uuid::fromString('0da3d8a5-453c-4f25-a901-b9500fa865c1'));

        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = $project->getId();
        $jsonCommand->name = "domainModel";
        $command = AddDomainModel::hydrateFromJson($jsonCommand);
        $commandHandler = new AddDomainModelHandler($projectRepo, $domainModelRepo, $attributeRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $domainModels = $domainModelRepo->getByProject($project);
        Assert::assertCount(3, $domainModels);
        return $domainModels;
    }

    /** @depends testMakeDomainModels */
    public function testChangeDomainModel(array $domainModels): array
    {
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var DomainModel $firstDomainModel */
        $firstDomainModel = $domainModels[0];

        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstDomainModel->getId();
        $jsonCommand->name = "changed name";
        $jsonCommand->namespace = "changed nameSpace";
        $jsonCommand->repository = "changed repository";
        $command = ChangeDomainModel::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeDomainModelHandler($domainModelRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $domainModel = $domainModelRepo->getById($firstDomainModel->getId());
        $domainModelRM = DomainModelRM::hydrateFromModel($domainModel, true);
        $result = json_decode(json_encode($domainModelRM));
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals('changed nameSpace', $result->namespace);
        Assert::assertEquals('changed repository', $result->repository);
        Assert::assertCount(2, $result->attributes);

        return $domainModels;
    }

    /** @depends testChangeDomainModel */
    public function testOrderDomainModels(array $domainModels): array
    {
        $currentOrder = [];
        foreach ($domainModels as $domainModel) {
            $currentOrder[] = $domainModel->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderDomainModel::hydrateFromJson($jsonCommand);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $commandHandler = new OrderDomainModelHandler($domainModelRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $projectRepo = $this->entityManager->getRepository(Project::class);
        $project = $projectRepo->getByUuid(Uuid::fromString('0da3d8a5-453c-4f25-a901-b9500fa865c1'));
        $domainModels = $domainModelRepo->getByProject($project);
        $resultOrder = [];
        foreach ($domainModels as $domainModel) {
            $resultOrder[] = $domainModel->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $domainModels;
    }

    /** @depends testOrderDomainModels */
    public function testDeleteDomainModel(array $domainModels): void
    {
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $associationRepo = $this->entityManager->getRepository(Association::class);
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var DomainModel $firstDomainModel */
        $firstDomainModel = $domainModels[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstDomainModel->getId();
        $command = DeleteDomainModel::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteDomainModelHandler($domainModelRepo, $attributeRepo, $associationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $project = $projectRepo->getByUuid(Uuid::fromString('0da3d8a5-453c-4f25-a901-b9500fa865c1'));
        $domainModels = $domainModelRepo->getByProject($project);
        Assert::assertCount(2, $domainModels);
    }
}
