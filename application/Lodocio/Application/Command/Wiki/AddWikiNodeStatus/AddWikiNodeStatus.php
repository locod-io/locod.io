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

namespace App\Lodocio\Application\Command\Wiki\AddWikiNodeStatus;

class AddWikiNodeStatus
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $wikiId,
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
            $json->wikiId,
            $json->name,
            $json->color,
            $json->isStart,
            $json->isFinal,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getWikiId(): int
    {
        return $this->wikiId;
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
