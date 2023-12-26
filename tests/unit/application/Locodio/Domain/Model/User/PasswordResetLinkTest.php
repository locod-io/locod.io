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

namespace App\Tests\unit\application\Locodio\Domain\Model\User;

use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\User;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class PasswordResetLinkTest extends TestCase
{
    private Uuid $uuid;
    private PasswordResetLink $link;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $user = User::make(
            $this->uuid,
            'user@test.com',
            'firstname',
            'lastname',
            []
        );
        $this->link = PasswordResetLink::make(
            $this->uuid,
            $user,
            'signature',
        );
        $this->link->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(PasswordResetLink::class, $this->link);
        Assert::assertEquals($this->uuid, $this->link->getUuid());
        Assert::assertEquals('user@test.com', $this->link->getUser()->getEmail());
        Assert::assertEquals('firstname', $this->link->getUser()->getFirstname());
        Assert::assertEquals('lastname', $this->link->getUser()->getLastname());
        Assert::assertEquals('signature', $this->link->getCode());
        Assert::assertTrue($this->link->isActive());
        Assert::assertFalse($this->link->isUsed());
    }

    public function testUseLink(): void
    {
        $this->link->useLink('signature');
        $this->link->setChecksum();
        Assert::assertFalse($this->link->isActive());
        Assert::assertTrue($this->link->isUsed());
    }

    public function testInValidate(): void
    {
        $this->link->inValidate();
        $this->link->setChecksum();
        Assert::assertFalse($this->link->isActive());
    }
}
