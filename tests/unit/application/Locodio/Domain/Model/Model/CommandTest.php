<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\Command;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CommandTest extends TestCase
{
    private Command $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Command::make(
            ModelFactory::makeProject(),
            $this->uuid,
            ModelFactory::makeDomainModel(),
            'commandName'
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Command::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('commandName', $this->model->getName());
    }

    public function testChange(): void
    {
        $this->model->change(
            ModelFactory::makeDomainModel(),
            'new command name',
            'command\namespace',
            ['mapping']
        );
        $this->model->setChecksum();
        Assert::assertEquals('new command name', $this->model->getName());
        Assert::assertEquals('command\namespace', $this->model->getNamespace());
        Assert::assertEquals(['mapping'], $this->model->getMapping());
    }
}
