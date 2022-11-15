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

use App\Locodio\Application\Command\Organisation\AddProject\AddProject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddProjectTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->organisationId = 1;
        $jsonCommand->name = 'project name';
        $command = AddProject::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getOrganisationId());
        Assert::assertEquals('project name', $command->getName());
    }
}
