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

namespace App\Lodocio\Application\Command\Wiki\DeleteWiki;

class DeleteWiki
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(protected int $id)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self($json->id);
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }
}
