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

namespace App\Tests\unit\application\Locodio\Application\Command\Model;

use App\Locodio\Application\Command\Model\ChangeField\ChangeField;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ChangeFieldTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->id = 1;
        $jsonCommand->name = "name";
        $jsonCommand->type = "type";
        $jsonCommand->length = 191;
        $jsonCommand->identifier = true;
        $jsonCommand->required = true;
        $jsonCommand->unique = true;
        $jsonCommand->make = false;
        $jsonCommand->change = false;
        $jsonCommand->enumId = 2;
        $command = ChangeField::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getId());
        Assert::assertEquals("name", $command->getName());
        Assert::assertEquals("type", $command->getType());
        Assert::assertEquals(191, $command->getLength());
        Assert::assertEquals(true, $command->isIdentifier());
        Assert::assertEquals(true, $command->isRequired());
        Assert::assertEquals(true, $command->isUnique());
        Assert::assertEquals(false, $command->isMake());
        Assert::assertEquals(false, $command->isChange());
        Assert::assertEquals(2, $command->getEnumId());
    }
}
