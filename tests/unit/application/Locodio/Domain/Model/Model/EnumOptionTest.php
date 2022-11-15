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

use App\Locodio\Domain\Model\Model\EnumOption;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class EnumOptionTest extends TestCase
{
    private EnumOption $model;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->model = EnumOption::make(
            ModelFactory::makeEnum(),
            $this->uuid,
            'code',
            'value'
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(EnumOption::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertEquals('code', $this->model->getCode());
        Assert::assertEquals('value', $this->model->getValue());
    }

    public function testChange(): void
    {
        $this->model->change(
            'changed code',
            'changed value',
        );
        $this->model->setChecksum();
        Assert::assertEquals('changed code', $this->model->getCode());
        Assert::assertEquals('changed value', $this->model->getValue());
    }
}
