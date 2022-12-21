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

namespace App\Locodio\Application\Query\Model\Readmodel;

use App\Locodio\Domain\Model\Model\ModelStatus;

class ModelStatusRM implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $uuid,
        protected string $name,
        protected string $color,
        protected bool   $isStart,
        protected bool   $isFinal,
        protected ?int   $usages = null,
        protected ?int   $sequence = null,
        protected ?float $x = null,
        protected ?float $y = null,
        protected ?array $flowIn = null,
        protected ?array $flowOut = null,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->isStart = $this->isStart();
        $json->isFinal = $this->isFinal();
        if (!is_null($this->getUsages())) {
            $json->usages = $this->getUsages();
        }
        if (!is_null($this->getSequence())) {
            $json->sequence = $this->getSequence();
        }
        if (!is_null($this->getX())) {
            $json->x = $this->getX();
        }
        if (!is_null($this->getY())) {
            $json->y = $this->getY();
        }
        if (!is_null($this->getFlowIn())) {
            $json->flowIn = $this->getFlowIn();
        }
        if (!is_null($this->getFlowOut())) {
            $json->flowOut = $this->getFlowOut();
        }
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(ModelStatus $model, bool $full = false): self
    {
        if ($full) {
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getName(),
                $model->getColor(),
                $model->isStart(),
                $model->isFinal(),
                null,
                $model->getSequence(),
                $model->getX(),
                $model->getY(),
                $model->getFlowIn(),
                $model->getFlowOut(),
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getName(),
                $model->getColor(),
                $model->isStart(),
                $model->isFinal(),
            );
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setUsages(int $usages): void
    {
        $this->usages = $usages;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function isStart(): bool
    {
        return $this->isStart;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function getFlowIn(): ?array
    {
        return $this->flowIn;
    }

    public function getFlowOut(): ?array
    {
        return $this->flowOut;
    }

    public function getUsages(): ?int
    {
        return $this->usages;
    }
}
