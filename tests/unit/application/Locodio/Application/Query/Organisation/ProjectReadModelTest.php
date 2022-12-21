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

namespace App\Tests\unit\application\Locodio\Application\Query\Organisation;

use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRMCollection;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ProjectReadModelTest extends TestCase
{
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = ModelFactory::makeProject();
        $this->project->identify(1, Uuid::fromString('dd11da44-aeea-46fa-ba69-03c874608af2'));
        $this->project->change('project', 'PRO', 'color');
        $this->project->setLayers('domain', 'application', 'infrastructure');
        $this->project->setLogo('some logo');
    }

    public function testReadModel(): void
    {
        $readModel = ProjectRM::hydrateFromModel($this->project);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('dd11da44-aeea-46fa-ba69-03c874608af2', $result->uuid);
        Assert::assertEquals('project', $result->name);
        Assert::assertEquals('PRO', $result->code);
        Assert::assertEquals('color', $result->color);
        Assert::assertEquals('domain', $result->domainLayer);
        Assert::assertEquals('application', $result->applicationLayer);
        Assert::assertEquals('infrastructure', $result->infrastructureLayer);
        Assert::assertEquals('some logo', $result->logo);
    }

    public function testReadModelCollection(): void
    {
        $readModel = ProjectRM::hydrateFromModel($this->project);
        $collection = new ProjectRMCollection();
        $collection->addItem($readModel);
        $result = json_decode(json_encode($collection));
        Assert::assertTrue(is_array($result->collection));
    }
}
