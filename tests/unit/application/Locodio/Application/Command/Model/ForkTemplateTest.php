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

use App\Locodio\Application\Command\Model\ForkTemplate\ForkTemplate;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ForkTemplateTest extends TestCase
{
    public function testCommand()
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->templateId = 1;
        $jsonCommand->userId = 2;
        $command = ForkTemplate::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getTemplateId());
        Assert::assertEquals(2, $command->getUserId());
    }
}
