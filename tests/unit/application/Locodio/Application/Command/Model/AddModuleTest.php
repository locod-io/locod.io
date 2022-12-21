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

use App\Locodio\Application\Command\Model\AddModule\AddModule;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddModuleTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = 1;
        $jsonCommand->name = 'name';
        $jsonCommand->namespace = 'namespace';
        $command = AddModule::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getProjectId());
        Assert::assertEquals('name', $command->getName());
        Assert::assertEquals('namespace', $command->getNamespace());
    }
}
