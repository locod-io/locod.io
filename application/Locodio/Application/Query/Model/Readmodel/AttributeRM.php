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

use App\Locodio\Domain\Model\Model\Attribute;

class AttributeRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int     $id,
        protected string  $uuid,
        protected int     $sequence,
        protected string  $name,
        protected int     $length,
        protected string  $type,
        protected bool    $identifier,
        protected bool    $required,
        protected bool    $unique,
        protected bool    $make,
        protected bool    $change,
        protected ?EnumRM $enum
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Attribute $model): self
    {
        $enum = null;
        if (!is_null($model->getEnum())) {
            $enum = EnumRM::hydrateFromModel($model->getEnum());
        }
        return new self(
            $model->getId(),
            $model->getUuidAsString(),
            $model->getSequence(),
            $model->getName(),
            $model->getLength(),
            $model->getType()->value,
            $model->isIdentifier(),
            $model->isRequired(),
            $model->isUnique(),
            $model->isMake(),
            $model->isChange(),
            $enum
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
        $json->sequence = $this->getSequence();
        $json->name = $this->getName();
        $json->length = $this->getLength();
        $json->type = $this->getType();
        $json->identifier = $this->isIdentifier();
        $json->required = $this->isRequired();
        $json->unique = $this->isUnique();
        $json->make = $this->isMake();
        $json->change = $this->isChange();
        $json->enum = $this->getEnum();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isIdentifier(): bool
    {
        return $this->identifier;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function isMake(): bool
    {
        return $this->make;
    }

    public function isChange(): bool
    {
        return $this->change;
    }

    public function getEnum(): ?EnumRM
    {
        return $this->enum;
    }
}
