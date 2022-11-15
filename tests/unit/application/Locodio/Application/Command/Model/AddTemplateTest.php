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

use App\Locodio\Application\Command\Model\AddTemplate\AddTemplate;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddTemplateTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = 1;
        $jsonCommand->type = "type";
        $jsonCommand->name = "name";
        $jsonCommand->language = "language";
        $command = AddTemplate::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getProjectId());
        Assert::assertEquals("type", $command->getType());
        Assert::assertEquals("name", $command->getName());
        Assert::assertEquals("language", $command->getLanguage());
    }
}
