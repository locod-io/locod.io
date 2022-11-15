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

use App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates\ImportTemplatesFromMasterTemplates;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ImportTemplatesFromMasterTemplatesTest extends TestCase
{
    public function testCommand()
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->projectId = 1;
        $jsonCommand->masterTemplateIds = [1, 2];
        $command = ImportTemplatesFromMasterTemplates::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getProjectId());
        Assert::assertEquals([1, 2], $command->getMasterTemplateIds());
    }
}
