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

namespace App\Locodio\Application\Command\User\InviteUser;

class InviteUser
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $organisationId,
        protected string $email,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydrate from JSON
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            organisationId: $json->organisationId,
            email: $json->email,
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }

}
