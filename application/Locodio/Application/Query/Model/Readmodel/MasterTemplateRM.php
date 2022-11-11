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

namespace App\Locodio\Application\Query\Model\Readmodel;

use App\Locodio\Application\Query\User\Readmodel\AnonymousUserRM;
use App\Locodio\Domain\Model\Model\MasterTemplate;
use DateTimeInterface;

class MasterTemplateRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int             $id,
        protected string          $uuid,
        protected string          $name,
        protected string          $type,
        protected string          $language,
        protected bool            $isPublic,
        protected AnonymousUserRM $user,
        protected string          $description,
        protected array           $tags,
        protected ?string         $template = null,
        protected ?\DateTime      $lastUpdated = null,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(MasterTemplate $model, bool $full = false): self
    {
        if ($full) {
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getTypeAsString(),
                $model->getLanguage(),
                $model->isPublic(),
                AnonymousUserRM::hydrateFromModel($model->getUser()),
                $model->getDescription(),
                $model->getTags(),
                $model->getTemplate(),
                $model->getUpdatedAt()
            );
        } else {
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getTypeAsString(),
                $model->getLanguage(),
                $model->isPublic(),
                AnonymousUserRM::hydrateFromModel($model->getUser()),
                $model->getDescription(),
                $model->getTags(),
            );
        }
        return $rm;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->name = $this->getName();
        $json->type = $this->getType();
        $json->language = $this->getLanguage();
        $json->isPublic = $this->isPublic();
        $json->description = $this->getDescription();
        $json->tags = $this->getTags();
        $json->from = $this->getUser();
        if (!is_null($this->getTemplate())) {
            $json->template = $this->getTemplate();
        }
        if (!is_null($this->getLastUpdated())) {
            $json->lastUpdatedAt = $this->getLastUpdated()->format(DateTimeInterface::ATOM);
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function getUser(): AnonymousUserRM
    {
        return $this->user;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getLastUpdated(): ?\DateTime
    {
        return $this->lastUpdated;
    }
}
