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

use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class MasterTemplateForkTest extends TestCase
{
    private MasterTemplateFork $model;
    private Uuid $uuid;
    private Uuid $sourceUuid;
    private Uuid $targetUuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->sourceUuid = Uuid::v4();
        $this->targetUuid = Uuid::v4();
        $this->model = MasterTemplateFork::make(
            $this->uuid,
            $this->sourceUuid,
            $this->targetUuid,
            'forkedBy'
        );
        $this->model->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(MasterTemplateFork::class, $this->model);
        Assert::assertEquals($this->uuid, $this->model->getUuid());
        Assert::assertInstanceOf(\DateTimeImmutable::class, $this->model->getForkedAt());
        Assert::assertEquals('forkedBy', $this->model->getForkedBy());
        Assert::assertEquals($this->sourceUuid, $this->model->getMasterTemplateSource());
        Assert::assertEquals($this->targetUuid, $this->model->getMasterTemplateTarget());
    }
}
