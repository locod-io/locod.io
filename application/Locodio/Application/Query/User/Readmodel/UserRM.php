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

use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Domain\Model\User\User;

class UserRM implements \JsonSerializable
{
    // ———————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                       $id,
        protected string                    $uuid,
        protected string                    $firstname,
        protected string                    $lastname,
        protected string                    $email,
        protected string                    $color,
        protected ?OrganisationRMCollection $organisations = null,
    ) {
    }

    // ———————————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(User $model, bool $full = false): self
    {
        if ($full) {
            $organisations = new OrganisationRMCollection();
            foreach ($model->getOrganisations() as $organisation) {
                $organisations->addItem(OrganisationRM::hydrateFromModel($organisation));
            }
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getFirstname(),
                $model->getLastname(),
                $model->getEmail(),
                $model->getColor(),
                $organisations
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getFirstname(),
                $model->getLastname(),
                $model->getEmail(),
                $model->getColor()
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
        $json->initials = strtoupper(substr($this->getFirstname(), 0, 1) . substr($this->getLastname(), 0, 1));
        if (!is_null($this->getOrganisations())) {
            $json->organisations = $this->getOrganisations()->getCollection();
        }
        return $json;
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

    public function getOrganisations(): ?OrganisationRMCollection
    {
        return $this->organisations;
    }
}
