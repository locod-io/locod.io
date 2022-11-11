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

use App\Locodio\Domain\Model\Model\DomainModel;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class DomainModelTest extends TestCase
{
    private DomainModel $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = DomainModel::make(
            ModelFactory::makeProject(),
            $this->uuid,
            'modelName',
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(DomainModel::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('modelName', $this->model->getName());
    }

    public function testChange(): void
    {
        $this->model->change(
            'newModelName',
            'namespace',
            'repository',
        );
        $this->model->setChecksum();
        Assert::assertEquals('newModelName', $this->model->getName());
        Assert::assertEquals('namespace', $this->model->getNamespace());
        Assert::assertEquals('repository', $this->model->getRepository());
    }
}
