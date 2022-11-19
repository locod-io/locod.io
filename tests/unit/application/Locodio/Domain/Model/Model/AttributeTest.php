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

use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\AttributeType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class AttributeTest extends TestCase
{
    private Attribute $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Attribute::make(
            ModelFactory::makeDomainModel(),
            $this->uuid,
            'fieldName',
            191,
            AttributeType::STRING,
            true,
            true,
            true,
            true,
            true,
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Attribute::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('fieldName', $this->model->getName());
        Assert::assertEquals(AttributeType::STRING, $this->model->getType());
        Assert::assertEquals(191, $this->model->getLength());
        Assert::assertEquals(true, $this->model->isIdentifier());
        Assert::assertEquals(true, $this->model->isUnique());
        Assert::assertEquals(true, $this->model->isRequired());
        Assert::assertEquals(true, $this->model->isMake());
        Assert::assertEquals(true, $this->model->isChange());
    }

    public function testChange(): void
    {
        $this->model->change(
            'new name',
            10,
            AttributeType::BOOLEAN,
            false,
            false,
            false,
            false,
            false
        );
        $this->model->setChecksum();
        Assert::assertEquals('new name', $this->model->getName());
        Assert::assertEquals(AttributeType::BOOLEAN, $this->model->getType());
        Assert::assertEquals(10, $this->model->getLength());
        Assert::assertEquals(false, $this->model->isIdentifier());
        Assert::assertEquals(false, $this->model->isUnique());
        Assert::assertEquals(false, $this->model->isRequired());
        Assert::assertEquals(false, $this->model->isMake());
        Assert::assertEquals(false, $this->model->isChange());
    }
}
