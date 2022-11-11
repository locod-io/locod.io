<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Relation;
use App\Locodio\Domain\Model\Model\RelationType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class RelationTest extends TestCase
{
    private Relation $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Relation::make(
            ModelFactory::makeDomainModel(),
            $this->uuid,
            RelationType::MTMB,
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
        Assert::assertInstanceOf(Relation::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals(RelationType::MTMB, $this->model->getType());
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
            RelationType::OTMB,
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
        Assert::assertEquals(RelationType::OTMB, $this->model->getType());
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
