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

use App\Locodio\Application\Command\Model\AddModelStatus\AddModelStatus;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddModelStatusTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = 1;
        $jsonCommand->name = 'name';
        $jsonCommand->color = 'color';
        $jsonCommand->isStart = true;
        $jsonCommand->isFinal = false;
        $command = AddModelStatus::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getProjectId());
        Assert::assertEquals('name', $command->getName());
        Assert::assertEquals('color', $command->getColor());
        Assert::assertEquals(true, $command->isStart());
        Assert::assertEquals(false, $command->isFinal());
    }
}
