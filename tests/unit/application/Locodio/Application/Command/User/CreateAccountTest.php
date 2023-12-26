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

use App\Locodio\Application\Command\User\CreateAccount\CreateAccount;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class CreateAccountTest extends TestCase
{
    public function testCommand(): void
    {
        $command = new CreateAccount('signature', 123456);
        Assert::assertEquals('signature', $command->getSignature());
        Assert::assertEquals(123456, $command->getCode());
    }
}
