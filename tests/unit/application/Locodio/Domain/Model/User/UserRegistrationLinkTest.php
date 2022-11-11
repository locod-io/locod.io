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

use App\Locodio\Domain\Model\User\UserRegistrationLink;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class UserRegistrationLinkTest extends TestCase
{
    private UserRegistrationLink $link;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->link = UserRegistrationLink::make(
            $this->uuid,
            'user@test.com',
            'organisation',
            'firstname',
            'lastname',
            'someSecurePassword'
        );
        $this->link->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(UserRegistrationLink::class, $this->link);
        Assert::assertEquals($this->uuid, $this->link->getUuid());
        Assert::assertEquals('organisation', $this->link->getOrganisation());
        Assert::assertEquals('firstname', $this->link->getFirstname());
        Assert::assertEquals('lastname', $this->link->getLastname());
        Assert::assertEquals('someSecurePassword', $this->link->getPassword());
        Assert::assertEquals(false, $this->link->isUsed());
    }

    public function testUseLink(): void
    {
        $this->link->useLink();
        $this->link->setChecksum();
        Assert::assertEquals(true, $this->link->isUsed());
        Assert::assertEquals('', $this->link->getPassword());
        Assert::assertEquals('**', $this->link->getOrganisation());
        Assert::assertEquals('**', $this->link->getFirstname());
        Assert::assertEquals('**', $this->link->getLastname());
        Assert::assertEquals('**', $this->link->getEmail());
    }
}
