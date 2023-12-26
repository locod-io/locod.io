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

namespace App\Locodio\Application\Query\User\Readmodel;

use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationPermissionsRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationPermissionsRMCollection;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Domain\Model\User\User;

class UserRM implements \JsonSerializable
{
    protected string $initials;

    // ———————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                                  $id,
        protected string                               $uuid,
        protected string                               $firstname,
        protected string                               $lastname,
        protected string                               $email,
        protected string                               $color,
        protected string                               $theme,
        protected string                               $userId,
        protected string                               $organisationLabel,
        protected bool                                 $hasLocodio = true,
        protected bool                                 $hasLodocio = true,
        protected ?OrganisationRMCollection            $organisations = null,
        protected ?OrganisationPermissionsRMCollection $organisationPermissions = null,
    ) {
        $this->initials = strtoupper(
            substr($this->firstname, 0, 1) . substr($this->lastname, 0, 1)
        );
    }

    // ———————————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(User $model, bool $full = false): self
    {
        $hasLocodio = false;
        $hasLodocio = false;
        if (isset($_SERVER['APP_HAS_LOCODIO']) && $_SERVER['APP_HAS_LOCODIO'] === 'true') {
            $hasLocodio = true;
        }
        if (isset($_SERVER['APP_HAS_LODOCIO']) && $_SERVER['APP_HAS_LODOCIO'] === 'true') {
            $hasLodocio = true;
        }

        if ($full) {
            $organisations = new OrganisationRMCollection();
            $organisationPermissions = new OrganisationPermissionsRMCollection();
            foreach ($model->getOrganisations() as $organisation) {
                $permissions = null;
                foreach ($model->getOrganisationUsers() as $organisationUser) {
                    if ($organisation->getId() === $organisationUser->getOrganisation()->getId()) {
                        $permissions = $organisationUser;
                        continue;
                    }
                }
                if (false === is_null($permissions)) {
                    $organisationPermissions->addItem(OrganisationPermissionsRM::hydrateFromModel($organisation, $permissions));
                }
                $organisations->addItem(OrganisationRM::hydrateFromModel($organisation));
            }
            return new self(
                id: $model->getId(),
                uuid: $model->getUuidAsString(),
                firstname: $model->getFirstname(),
                lastname: $model->getLastname(),
                email: $model->getEmail(),
                color: $model->getColor(),
                theme: $model->getThemeAsString(),
                userId: $model->getUserId(),
                organisationLabel: $_SERVER['APP_LABEL_ORGANISATION'],
                hasLocodio: $hasLocodio,
                hasLodocio: $hasLodocio,
                organisations: $organisations,
                organisationPermissions: $organisationPermissions,
            );
        } else {
            return new self(
                id: $model->getId(),
                uuid: $model->getUuidAsString(),
                firstname: $model->getFirstname(),
                lastname: $model->getLastname(),
                email: $model->getEmail(),
                color: $model->getColor(),
                theme: $model->getThemeAsString(),
                userId: $model->getUserId(),
                organisationLabel: $_SERVER['APP_LABEL_ORGANISATION'],
                hasLocodio: $hasLocodio,
                hasLodocio: $hasLodocio,
            );
        }
    }

    // ———————————————————————————————————————————————————————————————————
    // What to render as JSON
    // ———————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->email = $this->getEmail();
        $json->firstname = $this->getFirstname();
        $json->lastname = $this->getLastname();
        $json->color = $this->getColor();
        $json->theme = $this->getTheme();
        $json->initials = $this->getInitials();
        $json->userId = $this->getUserId();
        $json->organisationLabel = $this->getOrganisationLabel();
        $json->hasLocodio = $this->hasLocodio();
        $json->hasLodocio = $this->hasLodocio();
        if (!is_null($this->getOrganisations())) {
            $json->organisations = $this->getOrganisations()->getCollection();
        }
        if (!is_null($this->getOrganisationPermissions())) {
            $json->organisationPermissions = $this->getOrganisationPermissions()->getCollection();
        }
        return $json;
    }

    public function stripOrganisations(int $organisationId): void
    {

        $organisations = new OrganisationRMCollection();
        foreach ($this->organisations->getCollection() as $organisation) {
            if ($organisation->getId() === $organisationId) {
                $organisations->addItem($organisation);
            }
        }

        $organisationPermissions = new OrganisationPermissionsRMCollection();
        foreach ($this->organisationPermissions->getCollection() as $organisationPermission) {
            if ($organisationPermission->getId() === $organisationId) {
                $organisationPermissions->addItem($organisationPermission);
            }
        }

        $this->organisations = $organisations;
        $this->organisationPermissions = $organisationPermissions;
    }

    // ———————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getInitials(): string
    {
        return $this->initials;
    }

    public function getOrganisationLabel(): string
    {
        return $this->organisationLabel;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function hasLocodio(): bool
    {
        return $this->hasLocodio;
    }

    public function hasLodocio(): bool
    {
        return $this->hasLodocio;
    }

    public function getOrganisations(): ?OrganisationRMCollection
    {
        return $this->organisations;
    }

    public function getOrganisationPermissions(): ?OrganisationPermissionsRMCollection
    {
        return $this->organisationPermissions;
    }

}
