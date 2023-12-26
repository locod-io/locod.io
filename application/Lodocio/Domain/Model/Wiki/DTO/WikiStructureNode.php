<?php

namespace App\Lodocio\Domain\Model\Wiki\DTO;

use App\Lodocio\Domain\Model\Wiki\WikiNode;

class WikiStructureNode implements \JsonSerializable
{
    public function __construct(
        protected ?int   $id,
        protected string $uuid,
        protected int    $artefactId,
        protected string $name,
        protected string $number,
        protected int    $level,
        protected bool   $isOpen,
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
        return $json;
    }

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            $json->id,
            $json->uuid,
            $json->artefactId,
            $json->name,
            $json->number,
            $json->level,
            $json->isOpen,
        );
    }

    public static function hydrateFromModel(WikiNode $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getArtefactId(),
            $model->getName(),
            $model->getNumber(),
            $model->getLevel(),
            $model->isOpen(),
        );
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

    public function getLevel(): int
    {
        return $this->level;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

}
