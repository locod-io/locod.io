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

use App\Locodio\Application\Command\Model\AddMasterTemplate\AddMasterTemplate;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddMasterTemplateTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->userId = 1;
        $jsonCommand->type = "type";
        $jsonCommand->name = "name";
        $jsonCommand->language = "language";
        $command = AddMasterTemplate::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getUserId());
        Assert::assertEquals("type", $command->getType());
        Assert::assertEquals("name", $command->getName());
        Assert::assertEquals("language", $command->getLanguage());
    }
}
