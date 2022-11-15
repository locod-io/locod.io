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

use App\Locodio\Domain\Model\Model\Query;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class QueryTest extends TestCase
{
    private Query $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = Query::make(
            ModelFactory::makeProject(),
            $this->uuid,
            ModelFactory::makeDomainModel(),
            'query name',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Query::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('query name', $this->model->getName());
    }

    public function testChange(): void
    {
        $this->model->change(
            ModelFactory::makeDomainModel(),
            'new query name',
            'query\namespace',
            ['mapping']
        );
        $this->model->setChecksum();
        Assert::assertEquals('new query name', $this->model->getName());
        Assert::assertEquals('query\namespace', $this->model->getNamespace());
        Assert::assertEquals(['mapping'], $this->model->getMapping());
    }
}
