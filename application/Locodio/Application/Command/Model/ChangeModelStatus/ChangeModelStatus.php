<?php

declare(strict_types=1);

namespace App\Locodio\Application\Command\Model\ChangeModelStatus;

class ChangeModelStatus
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $id,
        protected string $name,
        protected string $color,
        protected bool   $isStart,
        protected bool   $isFinal,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->id,
            $json->name,
            $json->color,
            $json->isStart,
            $json->isFinal,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isStart(): bool
    {
        return $this->isStart;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }
}
