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

use App\Locodio\Application\Command\User\ChangeProfile\ChangeProfile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ChangeProfileTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = 1;
        $jsonCommand->firstname = 'firstname';
        $jsonCommand->lastname = 'lastname';
        $jsonCommand->color = 'color';
        $command = ChangeProfile::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getUserId());
        Assert::assertEquals('firstname', $command->getFirstname());
        Assert::assertEquals('lastname', $command->getLastname());
        Assert::assertEquals('color', $command->getColor());
    }
}
