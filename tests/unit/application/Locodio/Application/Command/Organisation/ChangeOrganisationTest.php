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

namespace App\Tests\unit\application\Locodio\Application\Command\Organisation;

use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisation;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ChangeOrganisationTest extends TestCase
{
    public function testCommand(): void
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->id = 1;
        $jsonCommand->name = 'organisation name';
        $jsonCommand->code = 'ORG';
        $jsonCommand->color = 'color';
        $jsonCommand->linearApiKey = 'some-key';
        $jsonCommand->figmaApiKey = 'figma-key';
        $jsonCommand->slug = 'some-slug';
        $command = ChangeOrganisation::hydrateFromJson($jsonCommand);
        Assert::assertEquals(1, $command->getId());
        Assert::assertEquals('organisation name', $command->getName());
        Assert::assertEquals('ORG', $command->getCode());
        Assert::assertEquals('color', $command->getColor());
        Assert::assertEquals('some-slug', $command->getSlug());
        Assert::assertEquals('some-key', $command->getLinearApiKey());
        Assert::assertEquals('figma-key', $command->getFigmaApiKey());
    }
}
