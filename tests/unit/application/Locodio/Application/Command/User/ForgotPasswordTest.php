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

use App\Locodio\Application\Command\User\ForgotPassword\ForgotPassword;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ForgotPasswordTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->email = 'test@test.com';
        $command = ForgotPassword::hydrateFromJson($jsonCommand);
        Assert::assertEquals('test@test.com', $command->getEmail());
    }
}
