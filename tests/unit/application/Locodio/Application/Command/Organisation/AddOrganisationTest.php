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

namespace App\Tests\unit\application\Locodio\Application\Command\Organisation;

use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisation;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddOrganisationTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = 1;
        $jsonCommand->name = 'organisation';
        $command = AddOrganisation::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getUserId());
        Assert::assertEquals('organisation', $command->getName());
    }
}
