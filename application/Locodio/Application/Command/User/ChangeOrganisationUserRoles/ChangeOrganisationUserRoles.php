<?php

namespace App\Locodio\Application\Command\User\ChangeOrganisationUserRoles;

class ChangeOrganisationUserRoles
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int   $organisationId,
        protected int   $userId,
        protected array $roles
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydration
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            $json->organisationId,
            $json->userId,
            $json->roles
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

}
