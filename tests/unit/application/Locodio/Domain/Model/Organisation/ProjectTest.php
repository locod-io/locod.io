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

namespace App\Tests\unit\application\Locodio\Domain\Model\Organisation;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ProjectTest extends TestCase
{
    private Project $project;
    private Uuid $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = Uuid::v4();
        $organisation = Organisation::make(Uuid::v4(), 'organisation', 'ORG');
        $this->project = Project::make($this->uuid, 'project', 'code', $organisation);
        $this->project->setChecksum();
    }

    public function testMake(): void
    {
        Assert::assertInstanceOf(Project::class, $this->project);
        Assert::assertEquals($this->uuid, $this->project->getUuid());
        Assert::assertEquals('project', $this->project->getName());
        Assert::assertEquals('code', $this->project->getCode());
        Assert::assertEquals('ORG', $this->project->getOrganisation()->getCode());
        Assert::assertEquals('organisation', $this->project->getOrganisation()->getName());
    }

    public function testChange(): void
    {
        $this->project->change('new project', 'NEW-CODE', '#FFFFFF', 'some-slug', 'gitRepo');
        $this->project->setChecksum();
        Assert::assertEquals('new project', $this->project->getName());
        Assert::assertEquals('NEW-CODE', $this->project->getCode());
        Assert::assertEquals('#FFFFFF', $this->project->getColor());
        Assert::assertEquals('some-slug', $this->project->getSlug());
        Assert::assertEquals('gitRepo', $this->project->getGitRepository());
    }

    public function testChangeLayers(): void
    {
        $this->project->setLayers('domainLayer', 'applicationLayer', 'infrastructureLayer');
        $this->project->setChecksum();
        Assert::assertEquals('domainLayer', $this->project->getDomainLayer());
        Assert::assertEquals('applicationLayer', $this->project->getApplicationLayer());
        Assert::assertEquals('infrastructureLayer', $this->project->getInfrastructureLayer());
    }

    public function testSetLogo(): void
    {
        $this->project->setLogo('some logo');
        $this->project->setChecksum();
        Assert::assertEquals('some logo', $this->project->getLogo());
    }
}
