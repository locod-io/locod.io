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

namespace App\Tests\unit\application\Locodio\Domain\Model\Organisation;

use App\Locodio\Domain\Model\Organisation\Organisation;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class OrganisationTest extends TestCase
{
    private Organisation $organisation;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->organisation = Organisation::make($this->uuid, 'organisation', 'ORG');
        $this->organisation->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Organisation::class, $this->organisation);
        Assert::assertEquals($this->uuid, $this->organisation->getUuid());
        Assert::assertEquals('organisation', $this->organisation->getName());
        Assert::assertEquals('ORG', $this->organisation->getCode());
    }

    public function testChange(): void
    {
        $this->organisation->change('new organisation', 'NEW-CODE', '#FFFFFF', 'some-key');
        $this->organisation->setChecksum();
        Assert::assertEquals('new organisation', $this->organisation->getName());
        Assert::assertEquals('NEW-CODE', $this->organisation->getCode());
        Assert::assertEquals('#FFFFFF', $this->organisation->getColor());
        Assert::assertEquals('some-key', $this->organisation->getLinearApiKey());
    }
}
