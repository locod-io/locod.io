<?php

namespace App\Lodocio\Domain\Model\Wiki\DTO;

class WikiStructure implements \JsonSerializable
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

    public function addGroup(WikiStructureGroup $group): void
    {
        $this->groups[] = $group;
    }

    public function addNode(WikiStructureNode $node): void
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
