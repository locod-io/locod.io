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

use App\Locodio\Application\Command\User\SendRegistrationMail\SendRegistrationMail;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class SendRegistrationMailTest extends TestCase
{
    public function testCommand(): void
    {
        $command = new SendRegistrationMail(
            'en',
            'bd34f852-3828-4b97-8413-c1032a87c5e0',
            'https://localhost'
        );
        Assert::assertEquals('en', $command->getLocale());
        Assert::assertEquals('bd34f852-3828-4b97-8413-c1032a87c5e0', $command->getLinkUuid());
        Assert::assertEquals('https://localhost', $command->getHost());
    }
}
