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

namespace App\Locodio\Application\Command\Model\AddRelation;

class AddRelation
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $domainModelId,
        protected string $type,
        protected string $mappedBy,
        protected string $inversedBy,
        protected string $fetch,
        protected string $orderBy,
        protected string $orderDirection,
        protected int    $targetDomainModelId
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->domainModelId,
            $json->type,
            $json->mappedBy,
            $json->inversedBy,
            $json->fetch,
            $json->orderBy,
            $json->orderDirection,
            $json->targetDomainModelId
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getDomainModelId(): int
    {
        return $this->domainModelId;
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
}
