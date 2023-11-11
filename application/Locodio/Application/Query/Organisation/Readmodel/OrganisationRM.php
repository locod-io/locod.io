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

namespace App\Locodio\Application\Query\Organisation\Readmodel;

use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Application\Query\User\Readmodel\UserRMCollection;
use App\Locodio\Domain\Model\Organisation\Organisation;
use Symfony\Component\Uid\Uuid;

class OrganisationRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                  $id,
        protected string               $uuid,
        protected string               $code,
        protected string               $name,
        protected string               $color,
        protected string               $slug,
        protected string               $linearApiKey,
        protected ?ProjectRMCollection $projects = null,
        protected ?UserRMCollection    $users = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Organisation $model, bool $full = false): self
    {
        if ($full) {
            $projects = new ProjectRMCollection();
            foreach ($model->getProjects() as $project) {
                $projects->addItem(ProjectRM::hydrateFromModel($project));
            }
            $users = new UserRMCollection();
            foreach ($model->getUsers() as $user) {
                $users->addItem(UserRM::hydrateFromModel($user));
            }
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getSlug(),
                $model->getLinearApiKey(),
                $projects,
                $users
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getSlug(),
                $model->getLinearApiKey()
            );
        }
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->code = $this->getCode();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->slug = $this->getSlug();
        $json->linearApiKey = hash("sha256", Uuid::v4()->toRfc4122());
        if (!is_null($this->getProjects())) {
            $json->projects = $this->getProjects()->getCollection();
        }
        if (!is_null($this->getUsers())) {
            $json->users = $this->getUsers()->getCollection();
        }
        return $json;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
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

    public function getLinearApiKey(): string
    {
        return $this->linearApiKey;
    }

    public function getProjects(): ?ProjectRMCollection
    {
        return $this->projects;
    }

    public function getUsers(): ?UserRMCollection
    {
        return $this->users;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

}
