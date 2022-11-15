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

use App\Locodio\Application\Command\Model\AddEnumOption\AddEnumOption;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AddEnumOptionTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->enumId = 1;
        $jsonCommand->code = "enumCode";
        $jsonCommand->value = "enumValue";
        $command = AddEnumOption::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getEnumId());
        Assert::assertEquals("enumCode", $command->getCode());
        Assert::assertEquals("enumValue", $command->getValue());
    }
}