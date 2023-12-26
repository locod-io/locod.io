<?php

namespace App\Tests\unit\application\Lodocio\Domain\Tracker;

use PHPUnit\Framework\TestCase;
use App\Lodocio\Domain\Model\Tracker\Tracker;
use App\Lodocio\Domain\Model\Project\DocProject;
use Symfony\Component\Uid\Uuid;

class TrackerTest extends TestCase
{
    public function testMake()
    {
        $project = $this->createMock(DocProject::class);
        $uuid = Uuid::v4();
        $name = 'Test Tracker';
        $code = 'TEST';
        $color = '#000000';

        $tracker = Tracker::make($project, $uuid, $name, $code, $color);

        $this->assertInstanceOf(Tracker::class, $tracker);
        $this->assertEquals($project, $tracker->getProject());
        $this->assertEquals($uuid, $tracker->getUuid());
        $this->assertEquals($name, $tracker->getName());
        $this->assertEquals($code, $tracker->getCode());
        $this->assertEquals($color, $tracker->getColor());
    }

    public function testChange()
    {
        $project = $this->createMock(DocProject::class);
        $uuid = Uuid::v4();
        $name = 'Test Tracker';
        $code = 'TEST';
        $color = '#000000';
        $description = 'Test description';
        $relatedTeams = ['Team A', 'Team B'];
        $slug = 'test-tracker';
        $isPublic = true;
        $showOnlyFinalNodes = false;

        $tracker = Tracker::make($project, $uuid, $name, $code, $color);
        $tracker->change($name, $code, $color, $description, $relatedTeams, $slug, $isPublic, $showOnlyFinalNodes);

        $this->assertEquals($name, $tracker->getName());
        $this->assertEquals($code, $tracker->getCode());
        $this->assertEquals($color, $tracker->getColor());
        $this->assertEquals($description, $tracker->getDescription());
        $this->assertEquals($relatedTeams, $tracker->getRelatedTeams());
        $this->assertEquals($slug, $tracker->getSlug());
        $this->assertEquals($isPublic, $tracker->isPublic());
        $this->assertEquals($showOnlyFinalNodes, $tracker->showOnlyFinalNodes());
    }

    //    public function testSetStructure()
    //    {
    //        $project = $this->createMock(DocProject::class);
    //        $uuid = Uuid::v4();
    //        $name = 'Test Tracker';
    //        $code = 'TEST';
    //        $color = '#000000';
    //
    //        $structure = ['field1' => 'value1', 'field2' => 'value2'];
    //
    //        $tracker = Tracker::make($project, $uuid, $name, $code, $color);
    //        $tracker->setStructure($structure);
    //
    //        $this->assertEquals($structure, $tracker->getStructure());
    //    }

    public function testSetRawStructure()
    {
        $project = $this->createMock(DocProject::class);
        $uuid = Uuid::v4();
        $name = 'Test Tracker';
        $code = 'TEST';
        $color = '#000000';
        $structure = (object) ['field1' => 'value1', 'field2' => 'value2'];

        $tracker = Tracker::make($project, $uuid, $name, $code, $color);
        $tracker->setRawStructure($structure);

        $this->assertEquals($structure, $tracker->getStructure());
    }
}
