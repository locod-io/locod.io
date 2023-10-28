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

use App\Locodio\Domain\Model\Model\Documentor;

class DocumentorRM implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                 $id,
        protected string              $uuid,
        protected string              $type,
        protected ModelStatusRM       $status,
        protected ?string             $description = null,
        protected ?\stdClass          $overview = null,
        protected ?string             $image = null,
        protected ?\DateTimeImmutable $finalAt = null,
        protected ?string             $finalBy = null,
        protected ?array              $linearIssues = null,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->status = $this->getStatus();
        $json->type = $this->getType();
        if (!is_null($this->getDescription())) {
            $json->description = $this->getDescription();
        }
        if (!is_null($this->getOverview())) {
            $json->schema = $this->getOverview();
        }
        if (!is_null($this->getImage())) {
            $json->image = $this->getImage();
        }
        if (!is_null($this->getFinalAt())) {
            $json->finalAt = $this->getFinalAt()->format(\DateTimeInterface::ATOM);
        }
        if (!is_null($this->getFinalBy())) {
            $json->finalBy = $this->getFinalBy();
        }
        if (!is_null($this->getLinearIssues())) {
            $json->linearIssues = $this->getLinearIssues();
        }

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(?Documentor $model, bool $full = false): self
    {
        if (is_null($model)) {
            $defaultModelStatusRM = new ModelStatusRM(
                0,
                '00-00',
                'undefined',
                'CCCCCC',
                true,
                false
            );
            return new self(0, '00-00', 'undefined', $defaultModelStatusRM);
        } else {
            if ($full) {
                return new self(
                    $model->getId(),
                    $model->getUuid()->toRfc4122(),
                    $model->getType()->value,
                    ModelStatusRM::hydrateFromModel($model->getStatus(), true),
                    $model->getDescription(),
                    $model->getOverview(),
                    $model->getImage(),
                    $model->getFinalAt(),
                    $model->getFinalBy(),
                    $model->getLinearIssues(),
                );
            } else {
                return new self(
                    $model->getId(),
                    $model->getUuid()->toRfc4122(),
                    $model->getType()->value,
                    ModelStatusRM::hydrateFromModel($model->getStatus()),
                );
            }
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): ModelStatusRM
    {
        return $this->status;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOverview(): ?\stdClass
    {
        return $this->overview;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getFinalAt(): ?\DateTimeImmutable
    {
        return $this->finalAt;
    }

    public function getFinalBy(): ?string
    {
        return $this->finalBy;
    }

    public function getLinearIssues(): ?array
    {
        return $this->linearIssues;
    }

}
