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

use App\Locodio\Application\Query\Model\Readmodel\FieldRM;
use App\Locodio\Application\Query\Model\Readmodel\FieldRMCollection;
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\FieldType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class FieldReadModelTest extends TestCase
{
    private Field $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Field::make(
            ModelFactory::makeDomainModel(),
            Uuid::v4(),
            'name',
            191,
            FieldType::STRING,
            true,
            true,
            true,
            false,
            false
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->model->change(
            'name',
            191,
            FieldType::STRING,
            true,
            true,
            true,
            false,
            false
        );
        $this->model->setEnum(ModelFactory::makeEnum());
    }

    public function testReadModel(): void
    {
        $readModel = FieldRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals(191, $result->length);
        Assert::assertEquals(FieldType::STRING->value, $result->type);
        Assert::assertEquals(true, $result->identifier);
        Assert::assertEquals(true, $result->required);
        Assert::assertEquals(true, $result->unique);
        Assert::assertEquals(false, $result->make);
        Assert::assertEquals(false, $result->change);
    }

    public function testReadModelCollection(): void
    {
        $readModel = FieldRM::hydrateFromModel($this->model);
        $collection = new FieldRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
