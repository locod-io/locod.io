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

use App\Locodio\Application\Command\Organisation\ImportProject\ImportProject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ImportProjectTest extends TestCase
{
    public function testCommand(): void
    {
        $project = new \stdClass();
        $project->name = 'project name';
        $project->code = 'PRO';
        $command = new ImportProject('18917075-79e6-4c87-b7f4-fb85b4caeaa7', $project);
        Assert::assertEquals('18917075-79e6-4c87-b7f4-fb85b4caeaa7', $command->getProjectUuid());
        Assert::assertEquals($project, $command->getImportProject());
    }
}
