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

use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class EnumTest extends TestCase
{
    private Enum $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Enum::make(
            ModelFactory::makeProject(),
            $this->uuid,
            ModelFactory::makeDomainModel(),
            'enumName',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Enum::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('enumName', $this->model->getName());
        Assert::assertInstanceOf(DomainModel::class, $this->model->getDomainModel());
    }

    public function testChange(): void
    {
        $this->model->change(
            ModelFactory::makeDomainModel(),
            'newName',
            'newNamespace'
        );
        $this->model->setChecksum();
        Assert::assertEquals('newName', $this->model->getName());
        Assert::assertEquals('newNamespace', $this->model->getNamespace());
        Assert::assertInstanceOf(DomainModel::class, $this->model->getDomainModel());
    }
}
