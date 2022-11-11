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

namespace App\Locodio\Application\Command\Model\ChangeField;

class ChangeField
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $id,
        protected string $name,
        protected string $type,
        protected int    $length,
        protected bool   $identifier,
        protected bool   $required,
        protected bool   $unique,
        protected bool   $make,
        protected bool   $change,
        protected int    $enumId,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->id,
            $json->name,
            $json->type,
            intval($json->length),
            $json->identifier,
            $json->required,
            $json->unique,
            $json->make,
            $json->change,
            $json->enumId
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLength(): int
    {
        return $this->length;
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

    public function getEnumId(): int
    {
        return $this->enumId;
    }
}
