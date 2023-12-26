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

namespace App\Lodocio\Application\Command\Wiki\ChangeWiki;

class ChangeWiki
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $id,
        protected string $code,
        protected string $name,
        protected string $color,
        protected string $description,
        protected array  $relatedTeams,
        protected string $slug,
        protected bool   $isPublic,
        protected bool   $showOnlyFinalNodes,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->id,
            $json->code,
            $json->name,
            $json->color,
            $json->description,
            $json->relatedTeams,
            trim($json->slug),
            (bool)$json->isPublic,
            (bool)$json->showOnlyFinalNodes,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRelatedTeams(): array
    {
        return $this->relatedTeams;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function showOnlyFinalNodes(): bool
    {
        return $this->showOnlyFinalNodes;
    }

}
