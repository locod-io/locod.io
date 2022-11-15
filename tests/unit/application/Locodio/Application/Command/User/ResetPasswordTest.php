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

use App\Locodio\Application\Command\User\ResetPassword\ResetPassword;
use App\Locodio\Application\Command\User\ResetPassword\ResetPasswordHash;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ResetPasswordTest extends TestCase
{
    public function testCommandWeakPassword(): void
    {
        $command = new ResetPassword('bd34f852-3828-4b97-8413-c1032a87c5e0', 'password');
        Assert::assertEquals('bd34f852-3828-4b97-8413-c1032a87c5e0', $command->getUuid());
        Assert::assertEquals('password', $command->getNewPlainPassword());
        Assert::assertEquals(false, $command->isPasswordValid());
    }

    public function testCommandStrongPassword(): void
    {
        $command = new ResetPassword('bd34f852-3828-4b97-8413-c1032a87c5e0', 'yRk1nM772fdEXNz');
        Assert::assertEquals('bd34f852-3828-4b97-8413-c1032a87c5e0', $command->getUuid());
        Assert::assertEquals('yRk1nM772fdEXNz', $command->getNewPlainPassword());
        Assert::assertEquals(true, $command->isPasswordValid());
    }

    public function testCommandHashWeakPassword(): void
    {
        $command = new ResetPasswordHash('hash', 'password');
        Assert::assertEquals('hash', $command->getHash());
        Assert::assertEquals('password', $command->getNewPlainPassword());
        Assert::assertEquals(false, $command->isPasswordValid());
    }

    public function testCommandHashStrongPassword(): void
    {
        $command = new ResetPasswordHash('yRk1nM772fdEXNz', 'uKVsCYOTINUVizY');
        Assert::assertEquals('yRk1nM772fdEXNz', $command->getHash());
        Assert::assertEquals('uKVsCYOTINUVizY', $command->getNewPlainPassword());
        Assert::assertEquals(true, $command->isPasswordValid());
    }
}
