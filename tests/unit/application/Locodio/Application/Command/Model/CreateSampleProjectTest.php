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

use App\Locodio\Application\Command\Model\CreateSampleProject\CreateSampleProject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CreateSampleProjectTest extends TestCase
{
    public function testCommand(): void
    {
        $command = new CreateSampleProject('4d2016c0-967a-492b-8b54-2bb95f3da73d');
        Assert::assertEquals('4d2016c0-967a-492b-8b54-2bb95f3da73d', $command->getProjectUuid());
    }
}
