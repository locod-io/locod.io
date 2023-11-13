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

use App\Locodio\Application\Command\Organisation\ChangeProject\ChangeProject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ChangeProjectTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->id = 1;
        $jsonCommand->name = 'project name';
        $jsonCommand->code = 'PRO';
        $jsonCommand->color = 'color';
        $jsonCommand->slug = 'some-slug';
        $jsonCommand->domainLayer = 'domainLayer';
        $jsonCommand->applicationLayer = 'applicationLayer';
        $jsonCommand->infrastructureLayer = 'infrastructureLayer';
        $jsonCommand->gitRepository = 'gitRepo';
        $command = ChangeProject::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getId());
        Assert::assertEquals('project name', $command->getName());
        Assert::assertEquals('PRO', $command->getCode());
        Assert::assertEquals('color', $command->getColor());
        Assert::assertEquals('some-slug', $command->getSlug());
        Assert::assertEquals('domainLayer', $command->getDomainLayer());
        Assert::assertEquals('applicationLayer', $command->getApplicationLayer());
        Assert::assertEquals('infrastructureLayer', $command->getInfrastructureLayer());
        Assert::assertEquals('gitRepo', $command->getGitRepository());
    }
}
