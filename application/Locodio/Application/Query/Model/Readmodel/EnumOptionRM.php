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

use App\Locodio\Domain\Model\Model\EnumOption;

class EnumOptionRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $uuid,
        protected int    $artefactId,
        protected int    $sequence,
        protected string $code,
        protected string $value
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(EnumOption $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuidAsString(),
            $model->getArtefactId(),
            $model->getSequence(),
            $model->getCode(),
            $model->getValue()
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->artefactId = 'EO-'.$this->getArtefactId();
        $json->sequence = $this->getSequence();
        $json->code = $this->getCode();
        $json->value = $this->getValue();
        return $json;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

}
