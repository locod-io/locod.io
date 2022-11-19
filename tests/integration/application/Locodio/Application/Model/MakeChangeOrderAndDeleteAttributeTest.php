<?php

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\AddAttribute\AddAttribute;
use App\Locodio\Application\Command\Model\AddAttribute\AddAttributeHandler;
use App\Locodio\Application\Command\Model\ChangeAttribute\ChangeAttribute;
use App\Locodio\Application\Command\Model\ChangeAttribute\ChangeAttributeHandler;
use App\Locodio\Application\Command\Model\DeleteAttribute\DeleteAttribute;
use App\Locodio\Application\Command\Model\DeleteAttribute\DeleteAttributeHandler;
use App\Locodio\Application\Command\Model\OrderAttribute\OrderAttribute;
use App\Locodio\Application\Command\Model\OrderAttribute\OrderAttributeHandler;
use App\Locodio\Application\Query\Model\Readmodel\AttributeRM;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\AttributeType;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteAttributeTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeAttributes(): array
    {
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);
        $enumRepo = $this->entityManager->getRepository(Enum::class);

        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $domainModel = $modelFactory->makeDomainModel(Uuid::fromString('82c40a5a-b5d1-4884-b91b-2fe295e398f0'));
        $enum = $modelFactory->makeEnum(Uuid::fromString('1bb8c79a-1307-4e59-899f-d65311717292'));

        $jsonCommand = new \stdClass();
        $jsonCommand->domainModelId = $domainModel->getId();
        $jsonCommand->name = "name";
        $jsonCommand->length = 191;
        $jsonCommand->type = AttributeType::ENUM->value;
        $jsonCommand->identifier = true;
        $jsonCommand->required = true;
        $jsonCommand->unique = true;
        $jsonCommand->make = false;
        $jsonCommand->change = false;
        $jsonCommand->enumId = $enum->getId();
        $command = AddAttribute::hydrateFromJson($jsonCommand);

        // -- make three attributes
        $commandHandler = new AddAttributeHandler($domainModelRepo, $attributeRepo, $enumRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $attributes = $attributeRepo->getByDomainModel($domainModel);
        Assert::assertCount(3, $attributes);
        return $attributes;
    }

    /** @depends testMakeAttributes */
    public function testChangeAttribute(array $attributes): array
    {
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $enumRepo = $this->entityManager->getRepository(Enum::class);

        /** @var Attribute $firstAttribute */
        $firstAttribute = $attributes[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstAttribute->getId();
        $jsonCommand->name = "changed name";
        $jsonCommand->type = AttributeType::INTEGER->value;
        $jsonCommand->length = 10;
        $jsonCommand->identifier = true;
        $jsonCommand->required = false;
        $jsonCommand->unique = false;
        $jsonCommand->make = false;
        $jsonCommand->change = false;
        $jsonCommand->enumId = 0;
        $command = ChangeAttribute::hydrateFromJson($jsonCommand);

        $commandHandler = new ChangeAttributeHandler($attributeRepo, $enumRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $attribute = $attributeRepo->getById($firstAttribute->getId());
        $attributeRM = AttributeRM::hydrateFromModel($attribute);
        $result = json_decode(json_encode($attributeRM));
        Assert::assertEquals('changed name', $result->name);
        Assert::assertEquals(10, $result->length);
        Assert::assertEquals(AttributeType::INTEGER->value, $result->type);
        Assert::assertEquals(true, $result->identifier);
        Assert::assertEquals(false, $result->required);
        Assert::assertEquals(false, $result->unique);
        Assert::assertEquals(false, $result->make);
        Assert::assertEquals(false, $result->change);

        return $attributes;
    }

    /** @depends testChangeAttribute */
    public function testOrderAttributes(array $attributes): array
    {
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        $currentOrder = [];
        foreach ($attributes as $attribute) {
            $currentOrder[] = $attribute->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderAttribute::hydrateFromJson($jsonCommand);
        $commandHandler = new OrderAttributeHandler($attributeRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $domainModel = $domainModelRepo->getByUuid(Uuid::fromString('82c40a5a-b5d1-4884-b91b-2fe295e398f0'));
        $attributes = $attributeRepo->getByDomainModel($domainModel);
        $resultOrder = [];
        foreach ($attributes as $attribute) {
            $resultOrder[] = $attribute->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $attributes;
    }

    /** @depends testOrderAttributes */
    public function testDeleteAttribute(array $attributes): void
    {
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $domainModelRepo = $this->entityManager->getRepository(DomainModel::class);

        /** @var Attribute $attributes */
        $firstAttribute = $attributes[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstAttribute->getId();
        $command = DeleteAttribute::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteAttributeHandler($attributeRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $domainModel = $domainModelRepo->getByUuid(Uuid::fromString('82c40a5a-b5d1-4884-b91b-2fe295e398f0'));
        $attributes = $attributeRepo->getByDomainModel($domainModel);
        Assert::assertCount(2, $attributes);
    }
}
