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

use App\Locodio\Application\Command\Model\OrderTemplate\OrderTemplate;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class OrderTemplateTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->sequence = [1, 2];
        $command = OrderTemplate::hydrateFromJson($jsonCommand);
        Assert::assertEquals([1, 2], $command->getSequence());
    }
}
