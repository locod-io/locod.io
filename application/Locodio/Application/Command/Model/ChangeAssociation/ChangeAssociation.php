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

namespace App\Locodio\Application\Command\Model\ChangeAssociation;

class ChangeAssociation
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $id,
        protected string $type,
        protected string $mappedBy,
        protected string $inversedBy,
        protected string $fetch,
        protected string $orderBy,
        protected string $orderDirection,
        protected int    $targetDomainModelId,
        protected bool   $isMake,
        protected bool   $isChange,
        protected bool   $isRequired,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->id,
            $json->type,
            $json->mappedBy,
            $json->inversedBy,
            $json->fetch,
            $json->orderBy,
            $json->orderDirection,
            $json->targetDomainModelId,
            $json->make,
            $json->change,
            $json->required,
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMappedBy(): string
    {
        return $this->mappedBy;
    }

    public function getInversedBy(): string
    {
        return $this->inversedBy;
    }

    public function getFetch(): string
    {
        return $this->fetch;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    public function getTargetDomainModelId(): int
    {
        return $this->targetDomainModelId;
    }

    public function isMake(): bool
    {
        return $this->isMake;
    }

    public function isChange(): bool
    {
        return $this->isChange;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
