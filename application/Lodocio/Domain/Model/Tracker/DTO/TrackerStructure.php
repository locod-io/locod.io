<?php

namespace App\Lodocio\Domain\Model\Tracker\DTO;

class TrackerStructure implements \JsonSerializable
{
    public function __construct(
        protected array $nodes = [],
        protected array $groups = [],
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->nodes = $this->getNodes();
        $json->groups = $this->getGroups();
        return $json;
    }

    public function addGroup(TrackerStructureGroup $group): void
    {
        $this->groups[] = $group;
    }

    public function addNode(TrackerStructureNode $node): void
    {
        $this->nodes[] = $node;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }



}
