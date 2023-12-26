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

use App\Locodio\Application\Query\Model\Readmodel\AssociationRM;
use App\Locodio\Application\Query\Model\Readmodel\AssociationRMCollection;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\AssociationType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AssociationReadModelTest extends TestCase
{
    private Association $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Association::make(
            ModelFactory::makeDomainModel(),
            Uuid::v4(),
            AssociationType::MTMB,
            'mappedBy',
            'inversedBy',
            FetchType::EXTRA_LAZY,
            'orderBy',
            OrderType::DESC,
            ModelFactory::makeDomainModel()
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->model->change(
            AssociationType::MTMB,
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
        $readModel = AssociationRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals(AssociationType::MTMB->value, $result->type);
        Assert::assertEquals('mappedBy', $result->mappedBy);
        Assert::assertEquals('inversedBy', $result->inversedBy);
        Assert::assertEquals(FetchType::EXTRA_LAZY->value, $result->fetch);
        Assert::assertEquals('orderBy', $result->orderBy);
        Assert::assertEquals(OrderType::DESC->value, $result->orderDirection);
        Assert::assertFalse($result->make);
        Assert::assertFalse($result->change);
        Assert::assertTrue($result->required);
    }

    public function testReadModelCollection(): void
    {
        $readModel = AssociationRM::hydrateFromModel($this->model);
        $collection = new AssociationRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
