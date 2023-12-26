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
        Assert::assertFalse($command->isPasswordValid());
    }

    public function testCommandStrongPassword(): void
    {
        $command = new ResetPassword('bd34f852-3828-4b97-8413-c1032a87c5e0', 'yRk1nM772fdEXNz');
        Assert::assertEquals('bd34f852-3828-4b97-8413-c1032a87c5e0', $command->getUuid());
        Assert::assertEquals('yRk1nM772fdEXNz', $command->getNewPlainPassword());
        Assert::assertTrue($command->isPasswordValid());
    }

    public function testCommandHashWeakPassword(): void
    {
        $command = new ResetPasswordHash('signature',123456, 'password');
        Assert::assertEquals('signature', $command->getSignature());
        Assert::assertEquals(123456, $command->getVerificationCode());
        Assert::assertEquals('password', $command->getNewPlainPassword());
        Assert::assertFalse($command->isPasswordValid());
    }

    public function testCommandHashStrongPassword(): void
    {
        $command = new ResetPasswordHash('yRk1nM772fdEXNz',123456, 'uKVsCYOTINUVizY');
        Assert::assertEquals('yRk1nM772fdEXNz', $command->getSignature());
        Assert::assertEquals(123456, $command->getVerificationCode());
        Assert::assertEquals('uKVsCYOTINUVizY', $command->getNewPlainPassword());
        Assert::assertTrue($command->isPasswordValid());
    }
}
