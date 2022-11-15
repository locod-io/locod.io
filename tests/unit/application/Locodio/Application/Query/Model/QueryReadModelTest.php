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

use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Application\Query\Model\Readmodel\QueryRMCollection;
use App\Locodio\Domain\Model\Model\Query;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QueryReadModelTest extends TestCase
{
    private Query $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Query::make(
            ModelFactory::makeProject(),
            Uuid::v4(),
            ModelFactory::makeDomainModel(),
            'name'
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->model->change(
            ModelFactory::makeDomainModel(),
            'name',
            'namespace',
            ['mapping'],
        );
    }

    public function testReadModel(): void
    {
        $readModel = QueryRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals('namespace', $result->namespace);
        Assert::assertEquals(['mapping'], $result->mapping);
    }

    public function testReadModelCollection(): void
    {
        $readModel = QueryRM::hydrateFromModel($this->model);
        $collection = new QueryRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
