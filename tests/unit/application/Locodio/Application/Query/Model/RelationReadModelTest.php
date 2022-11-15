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

namespace App\Tests\unit\application\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\RelationRM;
use App\Locodio\Application\Query\Model\Readmodel\RelationRMCollection;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Relation;
use App\Locodio\Domain\Model\Model\RelationType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class RelationReadModelTest extends TestCase
{
    private Relation $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Relation::make(
            ModelFactory::makeDomainModel(),
            Uuid::v4(),
            RelationType::MTMB,
            'mappedBy',
            'inversedBy',
            FetchType::EXTRA_LAZY,
            'orderBy',
            OrderType::DESC,
            ModelFactory::makeDomainModel()
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->model->change(
            RelationType::MTMB,
            'mappedBy',
            'inversedBy',
            FetchType::EXTRA_LAZY,
            'orderBy',
            OrderType::DESC,
            ModelFactory::makeDomainModel(),
            false,
            false,
            true
        );
    }

    public function testReadModel(): void
    {
        $readModel = RelationRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals(RelationType::MTMB->value, $result->type);
        Assert::assertEquals('mappedBy', $result->mappedBy);
        Assert::assertEquals('inversedBy', $result->inversedBy);
        Assert::assertEquals(FetchType::EXTRA_LAZY->value, $result->fetch);
        Assert::assertEquals('orderBy', $result->orderBy);
        Assert::assertEquals(OrderType::DESC->value, $result->orderDirection);
        Assert::assertEquals(false, $result->make);
        Assert::assertEquals(false, $result->change);
        Assert::assertEquals(true, $result->required);
    }

    public function testReadModelCollection(): void
    {
        $readModel = RelationRM::hydrateFromModel($this->model);
        $collection = new RelationRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
