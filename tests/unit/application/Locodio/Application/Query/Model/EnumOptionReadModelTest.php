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

use App\Locodio\Application\Query\Model\Readmodel\EnumOptionRM;
use App\Locodio\Application\Query\Model\Readmodel\EnumOptionRMCollection;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EnumOptionReadModelTest extends TestCase
{
    private EnumOption $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = EnumOption::make(
            ModelFactory::makeEnum(),
            Uuid::v4(),
            'code',
            'value'
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
    }

    public function testReadModel(): void
    {
        $readModel = EnumOptionRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('code', $result->code);
        Assert::assertEquals('value', $result->value);
    }

    public function testReadModelCollection(): void
    {
        $readModel = EnumOptionRM::hydrateFromModel($this->model);
        $collection = new EnumOptionRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
