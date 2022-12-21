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

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
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

    public function testDocument(): void
    {
        $this->model->document(ModelFactory::makeDocumentor(DocumentorType::COMMAND));
        $this->model->setChecksum();
        Assert::assertEquals(DocumentorType::COMMAND, $this->model->getDocumentor()->getType());
        Assert::assertEquals('documentor', $this->model->getDocumentor()->getDescription());
    }
}
