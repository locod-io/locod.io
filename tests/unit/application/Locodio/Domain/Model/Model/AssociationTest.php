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

use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\AssociationType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class AssociationTest extends TestCase
{
    private Association $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Association::make(
            ModelFactory::makeDomainModel(),
            $this->uuid,
            AssociationType::MTMB,
            'mappedBy',
            'inversedBy',
            FetchType::EXTRA_LAZY,
            'orderBy',
            OrderType::ASC,
            ModelFactory::makeDomainModel(),
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Association::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals(AssociationType::MTMB, $this->model->getType());
        Assert::assertEquals('mappedBy', $this->model->getMappedBy());
        Assert::assertEquals('inversedBy', $this->model->getInversedBy());
        Assert::assertEquals(FetchType::EXTRA_LAZY, $this->model->getFetch());
        Assert::assertEquals('orderBy', $this->model->getOrderBy());
        Assert::assertEquals(OrderType::ASC, $this->model->getOrderDirection());
        Assert::assertEquals(false, $this->model->isMake());
        Assert::assertEquals(false, $this->model->isChange());
        Assert::assertEquals(false, $this->model->isRequired());
    }

    public function testChange(): void
    {
        $this->model->change(
            AssociationType::OTMB,
            'changedMappedBy',
            'changedInversedBy',
            FetchType::LAZY,
            'orderByChanged',
            OrderType::DESC,
            ModelFactory::makeDomainModel(),
            true,
            true,
            true,
        );
        $this->model->setChecksum();
        Assert::assertEquals(AssociationType::OTMB, $this->model->getType());
        Assert::assertEquals('changedMappedBy', $this->model->getMappedBy());
        Assert::assertEquals('changedInversedBy', $this->model->getInversedBy());
        Assert::assertEquals(FetchType::LAZY, $this->model->getFetch());
        Assert::assertEquals('orderByChanged', $this->model->getOrderBy());
        Assert::assertEquals(OrderType::DESC, $this->model->getOrderDirection());
        Assert::assertEquals(true, $this->model->isMake());
        Assert::assertEquals(true, $this->model->isChange());
        Assert::assertEquals(true, $this->model->isRequired());
    }
}
