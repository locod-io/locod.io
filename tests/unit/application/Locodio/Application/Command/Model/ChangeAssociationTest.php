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

use App\Locodio\Application\Command\Model\ChangeAssociation\ChangeAssociation;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ChangeAssociationTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->id = 1;
        $jsonCommand->type = "type";
        $jsonCommand->mappedBy = "mappedBy";
        $jsonCommand->inversedBy = "inversedBy";
        $jsonCommand->fetch = "fetch";
        $jsonCommand->orderBy = "orderBy";
        $jsonCommand->orderDirection = "orderDirection";
        $jsonCommand->targetDomainModelId = 2;
        $jsonCommand->make = false;
        $jsonCommand->change = true;
        $jsonCommand->required = false;
        $command = ChangeAssociation::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getId());
        Assert::assertEquals("type", $command->getType());
        Assert::assertEquals("mappedBy", $command->getMappedBy());
        Assert::assertEquals("inversedBy", $command->getInversedBy());
        Assert::assertEquals("fetch", $command->getFetch());
        Assert::assertEquals("orderBy", $command->getOrderBy());
        Assert::assertEquals("orderDirection", $command->getOrderDirection());
        Assert::assertEquals(2, $command->getTargetDomainModelId());
        Assert::assertEquals(false, $command->isMake());
        Assert::assertEquals(true, $command->isChange());
        Assert::assertEquals(false, $command->isRequired());
    }
}
