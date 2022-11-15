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

namespace App\Tests\unit\application\Locodio\Application\Command\User;

use App\Locodio\Application\Command\User\Register\Register;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class RegisterTest extends TestCase
{
    public function testCommandDummyPassword(): void
    {
        $command = new Register(
            'organisation',
            'firstname',
            'lastname',
            'test@test.com',
            'password',
            'password'
        );
        Assert::assertEquals('organisation', $command->getOrganisation());
        Assert::assertEquals('firstname', $command->getFirstname());
        Assert::assertEquals('lastname', $command->getLastname());
        Assert::assertEquals('test@test.com', $command->getEmail());
        Assert::assertEquals('password', $command->getPassword1());
        Assert::assertEquals('password', $command->getPassword2());
        Assert::assertEquals(false, $command->isPasswordValid());
    }

    public function testCommandStrongPassword(): void
    {
        $command = new Register(
            'organisation',
            'firstname',
            'lastname',
            'test@test.com',
            'AA7xjIdTVtN4sJi',
            'AA7xjIdTVtN4sJi'
        );
        Assert::assertEquals('organisation', $command->getOrganisation());
        Assert::assertEquals('firstname', $command->getFirstname());
        Assert::assertEquals('lastname', $command->getLastname());
        Assert::assertEquals('test@test.com', $command->getEmail());
        Assert::assertEquals('AA7xjIdTVtN4sJi', $command->getPassword1());
        Assert::assertEquals('AA7xjIdTVtN4sJi', $command->getPassword2());
        Assert::assertEquals(true, $command->isPasswordValid());
    }

    public function testDifferentPasswords(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $command = new Register(
            'organisation',
            'firstname',
            'lastname',
            'test@test.com',
            'password1',
            'password2'
        );
    }
}
