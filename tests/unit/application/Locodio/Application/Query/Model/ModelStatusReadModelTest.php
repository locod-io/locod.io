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

use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRM;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRMCollection;
use App\Locodio\Domain\Model\Model\ModelStatus;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ModelStatusReadModelTest extends TestCase
{
    private ModelStatus $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = ModelStatus::make(
            ModelFactory::makeProject(),
            Uuid::v4(),
            'name',
            'color',
            false,
            true,
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
    }

    public function testReadModel(): void
    {
        $readModel = ModelStatusRM::hydrateFromModel($this->model, true);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals('color', $result->color);
        Assert::assertEquals(false, $result->isStart);
        Assert::assertEquals(true, $result->isFinal);
    }

    public function testReadModelCollection(): void
    {
        $readModel = ModelStatusRM::hydrateFromModel($this->model);
        $collection = new ModelStatusRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
