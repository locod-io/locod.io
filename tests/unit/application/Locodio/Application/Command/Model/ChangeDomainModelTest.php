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

use App\Locodio\Application\Command\Model\ChangeDomainModel\ChangeDomainModel;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ChangeDomainModelTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->id = 1;
        $jsonCommand->name = "name";
        $jsonCommand->namespace = "nameSpace";
        $jsonCommand->repository = "repository";
        $command = ChangeDomainModel::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getId());
        Assert::assertEquals("name", $command->getName());
        Assert::assertEquals("nameSpace", $command->getNamespace());
        Assert::assertEquals("repository", $command->getRepository());
    }
}
