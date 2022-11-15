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

use App\Locodio\Application\Command\Model\AddQuery\AddQuery;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddQueryTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->domainModelId = 1;
        $jsonCommand->projectId = 2;
        $jsonCommand->name = "queryName";
        $command = AddQuery::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getDomainModelId());
        Assert::assertEquals(2, $command->getProjectId());
        Assert::assertEquals("queryName", $command->getName());
    }
}
