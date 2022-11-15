<?php

namespace App\Tests\integration\application\Locodio\Application\Model;

use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOption;
use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOptionHandler;
use App\Locodio\Application\Command\Model\ChangeEnumOption\ChangeEnumOption;
use App\Locodio\Application\Command\Model\ChangeEnumOption\ChangeEnumOptionHandler;
use App\Locodio\Application\Command\Model\DeleteEnumOption\DeleteEnumOption;
use App\Locodio\Application\Command\Model\DeleteEnumOption\DeleteEnumOptionHandler;
use App\Locodio\Application\Command\Model\OrderEnumOption\OrderEnumOption;
use App\Locodio\Application\Command\Model\OrderEnumOption\OrderEnumOptionHandler;
use App\Locodio\Application\Query\Model\Readmodel\EnumOptionRM;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Tests\integration\DatabaseModelFactory;
use App\Tests\integration\DatabaseTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Uid\Uuid;

class MakeChangeOrderAndDeleteEnumOptionTest extends DatabaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMakeEnumOptions(): array
    {
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);
        $modelFactory = new DatabaseModelFactory($this->entityManager);
        $enum = $modelFactory->makeEnum(Uuid::fromString('6d5f1275-d761-4355-8d4c-d2701fa93b33'));

        $jsonCommand = new \stdClass();
        $jsonCommand->enumId = $enum->getId();
        $jsonCommand->code = "enumCode";
        $jsonCommand->value = "enumValue";
        $command = AddEnumOption::hydrateFromJson($jsonCommand);
        $commandHandler = new AddEnumOptionHandler($enumRepo, $enumOptionRepo);

        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();
        $commandHandler->go($command);
        $this->entityManager->flush();

        $options = $enumOptionRepo->getByEnum($enum);
        Assert::assertCount(3, $options);
        return $options;
    }

    /** @depends testMakeEnumOptions */
    public function testChangeEnumOption(array $options): array
    {
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);

        /** @var EnumOption $firstOption */
        $firstOption = $options[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstOption->getId();
        $jsonCommand->code = "changed code";
        $jsonCommand->value = "changed value";
        $command = ChangeEnumOption::hydrateFromJson($jsonCommand);
        $commandHandler = new ChangeEnumOptionHandler($enumOptionRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        // -- test the changes via the readmodel
        $option = $enumOptionRepo->getById($firstOption->getId());
        $optionRM = EnumOptionRM::hydrateFromModel($option);
        $result = json_decode(json_encode($optionRM));
        Assert::assertEquals('changed code', $result->code);
        Assert::assertEquals('changed value', $result->value);

        return $options;
    }

    /** @depends testChangeEnumOption */
    public function testOrderEnumOption(array $options): array
    {
        $currentOrder = [];
        foreach ($options as $option) {
            $currentOrder[] = $option->getId();
        }
        $newOrder = array_reverse($currentOrder);
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = $newOrder;
        $command = OrderEnumOption::hydrateFromJson($jsonCommand);
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);
        $commandHandler = new OrderEnumOptionHandler($enumOptionRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $enum = $enumRepo->getByUuid(Uuid::fromString('6d5f1275-d761-4355-8d4c-d2701fa93b33'));
        $options = $enumOptionRepo->getByEnum($enum);
        $resultOrder = [];
        foreach ($options as $option) {
            $resultOrder[] = $option->getId();
        }
        Assert::assertEquals($newOrder, $resultOrder);

        return $options;
    }

    /** @depends testOrderEnumOption */
    public function testDeleteEnumOption(array $options): void
    {
        $enumRepo = $this->entityManager->getRepository(Enum::class);
        $enumOptionRepo = $this->entityManager->getRepository(EnumOption::class);

        /** @var EnumOption $firstOption */
        $firstOption = $options[0];
        $jsonCommand = new \stdClass();
        $jsonCommand->id = $firstOption->getId();
        $command = DeleteEnumOption::hydrateFromJson($jsonCommand);
        $commandHandler = new DeleteEnumOptionHandler($enumOptionRepo);
        $commandHandler->go($command);
        $this->entityManager->flush();

        $enum = $enumRepo->getByUuid(Uuid::fromString('6d5f1275-d761-4355-8d4c-d2701fa93b33'));
        $options = $enumOptionRepo->getByEnum($enum);
        Assert::assertCount(2, $options);
    }
}
