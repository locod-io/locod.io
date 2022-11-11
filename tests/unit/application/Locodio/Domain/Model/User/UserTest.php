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

use App\Locodio\Domain\Model\User\User;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class UserTest extends TestCase
{
    private User $user;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $this->user = User::make(
            $this->uuid,
            'user@test.com',
            'firstname',
            'lastname',
            []
        );
        $this->user->setChecksum();
    }

    public function testUserCanBeMade(): void
    {
        Assert::assertInstanceOf(User::class, $this->user);
        Assert::assertEquals('firstname', $this->user->getFirstname());
        Assert::assertEquals('lastname', $this->user->getLastname());
        Assert::assertEquals('user@test.com', $this->user->getEmail());
        Assert::assertEquals('user@test.com', $this->user->getUserIdentifier());
        Assert::assertEquals($this->uuid->toRfc4122(), $this->user->getUuidAsString());
        Assert::assertEquals($this->uuid, $this->user->getUuid());
        Assert::assertIsArray($this->user->getRoles());
        Assert::assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testUserCanBeChanged(): void
    {
        $this->user->change('new firstname', 'new lastname', '#EEEEEE');
        $this->user->setChecksum();
        Assert::assertEquals('new firstname', $this->user->getFirstname());
        Assert::assertEquals('new lastname', $this->user->getLastname());
        Assert::assertEquals('#EEEEEE', $this->user->getColor());
    }

    public function testPasswordCanBeSet(): void
    {
        $this->user->setPassword('somepassword');
        $this->user->setChecksum();
        Assert::assertEquals('somepassword', $this->user->getPassword());
    }
}
