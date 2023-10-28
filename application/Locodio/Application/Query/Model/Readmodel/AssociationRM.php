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

use App\Locodio\Domain\Model\Model\Association;

class AssociationRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int           $id,
        protected string        $uuid,
        protected int           $artefactId,
        protected int           $sequence,
        protected string        $type,
        protected string        $mappedBy,
        protected string        $inversedBy,
        protected string        $fetch,
        protected string        $orderBy,
        protected string        $orderDirection,
        protected DomainModelRM $targetDomainModel,
        protected bool          $make,
        protected bool          $change,
        protected bool          $required,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Association $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuidAsString(),
            $model->getArtefactId(),
            $model->getSequence(),
            $model->getType()->value,
            $model->getMappedBy(),
            $model->getInversedBy(),
            $model->getFetch()->value,
            $model->getOrderBy(),
            $model->getOrderDirection()->value,
            DomainModelRM::hydrateFromModel($model->getTargetDomainModel()),
            $model->isMake(),
            $model->isChange(),
            $model->isRequired()
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->artefactId = 'AS-'.$this->getArtefactId();
        $json->sequence = $this->getSequence();
        $json->type = $this->getType();
        $json->mappedBy = $this->getMappedBy();
        $json->inversedBy = $this->getInversedBy();
        $json->fetch = $this->getFetch();
        $json->orderBy = $this->getOrderBy();
        $json->orderDirection = $this->getOrderDirection();
        $json->targetDomainModel = $this->getTargetDomainModel();
        $json->make = $this->isMake();
        $json->change = $this->isChange();
        $json->required = $this->isRequired();

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

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMappedBy(): string
    {
        return $this->mappedBy;
    }

    public function getInversedBy(): string
    {
        return $this->inversedBy;
    }

    public function getFetch(): string
    {
        return $this->fetch;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    public function getTargetDomainModel(): DomainModelRM
    {
        return $this->targetDomainModel;
    }

    public function isMake(): bool
    {
        return $this->make;
    }

    public function isChange(): bool
    {
        return $this->change;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

}
