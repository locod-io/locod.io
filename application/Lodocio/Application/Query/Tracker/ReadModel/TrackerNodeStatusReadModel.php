<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Query\Tracker\ReadModel;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus;

class TrackerNodeStatusReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $uuid,
        protected int    $artefactId,
        protected int    $sequence,
        protected string $name,
        protected string $color,
        protected bool   $isStart,
        protected bool   $isFinal,
        protected array  $flowIn,
        protected array  $flowOut,
        protected int    $x,
        protected int    $y,
        protected int    $usages,
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
        $json->artefactId = $this->getArtefactId();
        $json->sequence = $this->getSequence();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->isStart = $this->isStart();
        $json->isFinal = $this->isFinal();
        $json->flowIn = $this->getFlowIn();
        $json->flowOut = $this->getFlowOut();
        $json->x = $this->getX();
        $json->y = $this->getY();
        $json->usages = $this->getUsages();

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(TrackerNodeStatus $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getArtefactId(),
            $model->getSequence(),
            $model->getName(),
            $model->getColor(),
            $model->isStart(),
            $model->isFinal(),
            $model->getFlowIn(),
            $model->getFlowOut(),
            $model->getX(),
            $model->getY(),
            0
        );
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

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

    public function getSequence(): int
    {
        return $this->sequence;
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

    public function getFlowIn(): array
    {
        return $this->flowIn;
    }

    public function getFlowOut(): array
    {
        return $this->flowOut;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getUsages(): int
    {
        return $this->usages;
    }

}
