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

use App\Locodio\Application\Command\Model\AddAttribute\AddAttribute;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddAttributeTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->domainModelId = 3;
        $jsonCommand->name = "name";
        $jsonCommand->length = 191;
        $jsonCommand->type = "string";
        $jsonCommand->identifier = true;
        $jsonCommand->required = true;
        $jsonCommand->unique = true;
        $jsonCommand->make = false;
        $jsonCommand->change = false;
        $jsonCommand->enumId = 1;
        $command = AddAttribute::hydrateFromJson($jsonCommand);
        Assert::assertEquals(3, $command->getDomainModelId());
        Assert::assertEquals("name", $command->getName());
        Assert::assertEquals(191, $command->getLength());
        Assert::assertEquals("string", $command->getType());
        Assert::assertEquals(true, $command->isIdentifier());
        Assert::assertEquals(true, $command->isUnique());
        Assert::assertEquals(false, $command->isMake());
        Assert::assertEquals(false, $command->isChange());
        Assert::assertEquals(1, $command->getEnumId());
    }
}
