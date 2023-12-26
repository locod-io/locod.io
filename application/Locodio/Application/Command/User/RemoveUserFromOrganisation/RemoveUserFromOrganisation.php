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

namespace App\Locodio\Application\Command\User\RemoveUserFromOrganisation;

class RemoveUserFromOrganisation
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected int $organisationId,
        protected int $userId,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydrate from JSON
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            organisationId: $json->organisationId,
            userId: $json->userId,
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

}
