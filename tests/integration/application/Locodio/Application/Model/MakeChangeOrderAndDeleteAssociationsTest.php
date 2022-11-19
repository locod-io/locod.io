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

use App\Locodio\Application\Command\Model\AddAssociation\AddAssociation;
use App\Locodio\Application\Command\Model\AddAssociation\AddAssociationHandler;
use App\Locodio\Application\Command\Model\ChangeAssociation\ChangeAssociation;
use App\Locodio\Application\Command\Model\ChangeAssociation\ChangeAssociationHandler;
use App\Locodio\Application\Command\Model\DeleteAssociation\DeleteAssociation;
use App\Locodio\Application\Command\Model\DeleteAssociation\DeleteAssociationHandler;
use App\Locodio\Application\Command\Model\OrderAssociation\OrderAssociation;
use App\Locodio\Application\Command\Model\OrderAssociation\OrderAssociationHandler;
use App\Locodio\Application\Query\Model\Readmodel\AssociationRM;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\AssociationType;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteAssociationsTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeAssociations(): array
    {
        $associationRepo = $this->entityManager->getRepository(Association::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $domainModel = $modelFactory->makeDomainModel(Uuid::fromString('6d1ee6a7-43c9-43db-990a-fee2f3dcab0c'));
        $targetDomainModel = $modelFactory->makeDomainModel(Uuid::fromString('9fb1e0a5-707a-48ac-abc7-2b60a48b9cb0'));

        $jsonCommand = new \stdClass();
        $jsonCommand->domainModelId = $domainModel->getId();
        $jsonCommand->type = AssociationType::MTMB->value;
        $jsonCommand->mappedBy = "mappedBy";
        $jsonCommand->inversedBy = "inversedBy";
        $jsonCommand->fetch = FetchType::EXTRA_LAZY->value;
        $jsonCommand->orderBy = "orderBy";
        $jsonCommand->orderDirection = OrderType::DESC->value;
        $jsonCommand->targetDomainModelId = $targetDomainModel->getId();
        $command = AddAssociation::hydrateFromJson($jsonCommand);

        // -- make three associations
        $commandHandler = new AddAssociationHandler($domainModelRepo, $associationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $associations = $associationRepo->getByDomainModel($domainModel);
        Assert::assertCount(3, $associations);
        return $associations;
    }

    /** @depends testMakeAssociations */
    public function testChangeAssociation(array $associations): array
    {
        $associationRepo = $this->entityManager->getRepository(Association::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var Association $firstAssociation */
        $firstAssociation = $associations[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstAssociation->getId();
        $jsonCommand->type = AssociationType::OTOS->value;
        $jsonCommand->mappedBy = "mappedBy";
        $jsonCommand->inversedBy = "inversedBy";
        $jsonCommand->fetch = FetchType::LAZY->value;
        $jsonCommand->orderBy = "orderBy";
        $jsonCommand->orderDirection = OrderType::ASC->value;
        $jsonCommand->targetDomainModelId = $firstAssociation->getTargetDomainModel()->getId();
        $jsonCommand->make = false;
        $jsonCommand->change = true;
        $jsonCommand->required = false;
        $command = ChangeAssociation::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeAssociationHandler($domainModelRepo, $associationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $association = $associationRepo->getById($firstAssociation->getId());
        $associationRM = AssociationRM::hydrateFromModel($association);
        $result = json_decode(json_encode($associationRM));
        Assert::assertEquals(AssociationType::OTOS->value, $result->type);
        Assert::assertEquals('mappedBy', $result->mappedBy);
        Assert::assertEquals('inversedBy', $result->inversedBy);
        Assert::assertEquals(FetchType::LAZY->value, $result->fetch);
        Assert::assertEquals('orderBy', $result->orderBy);
        Assert::assertEquals(OrderType::ASC->value, $result->orderDirection);
        Assert::assertEquals($firstAssociation->getTargetDomainModel()->getId(), $result->targetDomainModel->id);
        Assert::assertEquals(false, $result->make);
        Assert::assertEquals(true, $result->change);
        Assert::assertEquals(false, $result->required);

        return $associations;
    }

    /** @depends testChangeAssociation */
    public function testOrderAssociations(array $associations): array
    {
        $associationRepo = $this->entityManager->getRepository(Association::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        $currentOrder = [];
        foreach ($associations as $association) {
            $currentOrder[] = $association->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderAssociation::hydrateFromJson($jsonCommand);
        $commandHandler = new OrderAssociationHandler($associationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $domainModel = $domainModelRepo->getByUuid(Uuid::fromString('6d1ee6a7-43c9-43db-990a-fee2f3dcab0c'));
        $associations = $associationRepo->getByDomainModel($domainModel);
        $resultOrder = [];
        foreach ($associations as $association) {
            $resultOrder[] = $association->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $associations;
    }

    /** @depends testOrderAssociations */
    public function testDeleteAssociation(array $associations): void
    {
        $associationRepo = $this->entityManager->getRepository(Association::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var Association $associations */
        $firstAssociation = $associations[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstAssociation->getId();
        $command = DeleteAssociation::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteAssociationHandler($associationRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $domainModel = $domainModelRepo->getByUuid(Uuid::fromString('6d1ee6a7-43c9-43db-990a-fee2f3dcab0c'));
        $associations = $associationRepo->getByDomainModel($domainModel);
        Assert::assertCount(2, $associations);
    }
}
