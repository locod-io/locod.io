<?php

namespace App\Lodocio\Domain\Model\Tracker\DTO;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeGroup;

class TrackerStructureGroup implements \JsonSerializable
{
    public function __construct(
        protected ?int    $id,
        protected string $uuid,
        protected int    $artefactId,
        protected string $name,
        protected string $number,
        protected bool   $isOpen,
        protected int    $level,
        protected array  $nodes,
        protected array  $groups,
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->artefactId = $this->getArtefactId();
        $json->name = $this->getName();
        $json->number = $this->getNumber();
        $json->level = $this->getLevel();
        $json->isOpen = $this->isOpen();
        $json->nodes = $this->getNodes();
        $json->groups = $this->getGroups();
        return $json;
    }

    public static function hydrateFromModel(TrackerNodeGroup $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getArtefactId(),
            $model->getName(),
            $model->getNumber(),
            $model->isOpen(),
            $model->getLevel(),
            [],
            []
        );
    }

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            $json->id,
            $json->uuid,
            0,
            $json->name,
            $json->number,
            $json->isOpen,
            $json->level,
            [],
            [],
        );
    }

    public function addGroup(TrackerStructureGroup $group): void
    {
        $this->groups[] = $group;
    }

    public function addNode(TrackerStructureNode $node): void
    {
        $this->nodes[] = $node;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public function getLevel(): int
    {
        return $this->level;
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
