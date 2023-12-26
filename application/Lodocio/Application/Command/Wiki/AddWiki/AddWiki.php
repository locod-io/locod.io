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

namespace App\Lodocio\Application\Command\Wiki\AddWiki;

class AddWiki
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected string $code,
        protected string $name,
        protected string $color,
        protected int    $docProjectId,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        string $code,
        string $name,
        string $color,
        int    $docProjectId
    ): self {
        return new self(
            $code,
            $name,
            $color,
            $docProjectId,
        );
    }

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->code,
            $json->name,
            $json->color,
            $json->docProjectId,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

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

    public function getDocProjectId(): int
    {
        return $this->docProjectId;
    }

}
