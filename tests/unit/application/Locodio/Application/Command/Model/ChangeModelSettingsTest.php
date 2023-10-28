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

use App\Locodio\Application\Command\Model\ChangeModelSettings\ChangeModelSettings;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ChangeModelSettingsTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = 1;
        $jsonCommand->id = 2;
        $jsonCommand->domainLayer = 'domainLayer';
        $jsonCommand->applicationLayer = 'applicationLayer';
        $jsonCommand->infrastructureLayer = 'infrastructureLayer';
        $jsonCommand->teams = ['team1','team2'];
        $command = ChangeModelSettings::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getProjectId());
        Assert::assertEquals(2, $command->getId());
        Assert::assertEquals('domainLayer', $command->getDomainLayer());
        Assert::assertEquals('applicationLayer', $command->getApplicationLayer());
        Assert::assertEquals('infrastructureLayer', $command->getInfrastructureLayer());
        Assert::assertEquals(['team1','team2'], $command->getTeams());
    }
}
