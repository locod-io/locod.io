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

use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRMCollection;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DomainModelReadModelTest extends TestCase
{
    private DomainModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = DomainModel::make(
            ModelFactory::makeProject(),
            Uuid::v4(),
            'name'
        );
        $this->model->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->model->change(
            'name',
            'namespace',
            'repository',
        );
    }

    public function testReadModel(): void
    {
        $readModel = DomainModelRM::hydrateFromModel($this->model);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('name', $result->name);
        Assert::assertEquals('namespace', $result->namespace);
        Assert::assertEquals('repository', $result->repository);
    }

    public function testReadModelCollection(): void
    {
        $readModel = DomainModelRM::hydrateFromModel($this->model);
        $collection = new DomainModelRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
