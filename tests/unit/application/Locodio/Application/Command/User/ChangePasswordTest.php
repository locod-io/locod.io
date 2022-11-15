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

use App\Locodio\Application\Command\User\ChangePassword\ChangePassword;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ChangePasswordTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = 1;
        $jsonCommand->password1 = 'password1';
        $jsonCommand->password2 = 'password2';

        $command = ChangePassword::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getUserId());
        Assert::assertEquals('password1', $command->getPassword1());
        Assert::assertEquals('password2', $command->getPassword2());
        Assert::assertEquals(false, $command->isPasswordValid());
    }

    public function testNotValidPassword(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = 2;
        $jsonCommand->password1 = 'password';
        $jsonCommand->password2 = 'password';

        $command = ChangePassword::hydrateFromJson($jsonCommand);
        Assert::assertEquals(2, $command->getUserId());
        Assert::assertEquals('password', $command->getPassword1());
        Assert::assertEquals('password', $command->getPassword2());
        Assert::assertEquals(false, $command->isPasswordValid());
    }

    public function testStrongPassword(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = 2;
        $jsonCommand->password1 = 'UMPi4SZmc5nJdWs';
        $jsonCommand->password2 = 'UMPi4SZmc5nJdWs';

        $command = ChangePassword::hydrateFromJson($jsonCommand);
        Assert::assertEquals(2, $command->getUserId());
        Assert::assertEquals('UMPi4SZmc5nJdWs', $command->getPassword1());
        Assert::assertEquals('UMPi4SZmc5nJdWs', $command->getPassword2());
        Assert::assertEquals(true, $command->isPasswordValid());
    }
}
