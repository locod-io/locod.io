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

use App\Locodio\Application\Command\Model\AddDomainModel\AddDomainModel;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddDomainModelTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = 1;
        $jsonCommand->name = "domainModel";
        $command = AddDomainModel::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getProjectId());
        Assert::assertEquals("domainModel", $command->getName());
    }
}
