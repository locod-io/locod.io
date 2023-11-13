<?php

namespace App\Tests\unit\application\Lodocio\Domain\Tracker\DTO;

use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructureNode;
use PHPUnit\Framework\TestCase;

class TrackerStructureNodeTest extends TestCase
{
    public function testJsonSerialize()
    {
        $trackerNode = new TrackerStructureNode(
            1,
            '544287fa-4b61-45eb-9523-14332f074a21',
            123,
            'Node Name',
            'Node Number',
            2,
            true
        );

        $json = $trackerNode->jsonSerialize();

        $this->assertInstanceOf(\stdClass::class, $json);
        $this->assertEquals(1, $json->id);
        $this->assertEquals('544287fa-4b61-45eb-9523-14332f074a21', $json->uuid);
        $this->assertEquals(123, $json->artefactId);
        $this->assertEquals('Node Name', $json->name);
        $this->assertEquals('Node Number', $json->number);
        $this->assertEquals(2, $json->level);
        $this->assertTrue($json->isOpen);
    }

    public function testHydrateFromJson()
    {
        $json = new \stdClass();
        $json->id = 1;
        $json->uuid = '544287fa-4b61-45eb-9523-14332f074a21';
        $json->artefactId = 123;
        $json->name = 'Node Name';
        $json->number = 'Node Number';
        $json->level = 2;
        $json->isOpen = true;

        $trackerNode = TrackerStructureNode::hydrateFromJson($json);

        $this->assertInstanceOf(TrackerStructureNode::class, $trackerNode);
        $this->assertEquals(1, $trackerNode->getId());
        $this->assertEquals('544287fa-4b61-45eb-9523-14332f074a21', $trackerNode->getUuid());
        $this->assertEquals(123, $trackerNode->getArtefactId());
        $this->assertEquals('Node Name', $trackerNode->getName());
        $this->assertEquals('Node Number', $trackerNode->getNumber());
        $this->assertEquals(2, $trackerNode->getLevel());
        $this->assertTrue($trackerNode->isOpen());
    }

}
