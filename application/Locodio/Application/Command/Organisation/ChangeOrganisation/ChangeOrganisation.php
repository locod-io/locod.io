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

namespace App\Locodio\Application\Command\Organisation\ChangeOrganisation;

class ChangeOrganisation
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $name,
        protected string $code,
        protected string $color,
        protected string $linearApiKey,
        protected string $figmaApiKey,
        protected string $slug,
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
            $json->code,
            $json->color,
            $json->linearApiKey,
            $json->figmaApiKey,
            $json->slug,
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getLinearApiKey(): string
    {
        return $this->linearApiKey;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getFigmaApiKey(): string
    {
        return $this->figmaApiKey;
    }
}
