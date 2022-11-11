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

use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Domain\Model\Model\DomainModel;

class DomainModelRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                   $id,
        protected string                $uuid,
        protected int                   $sequence,
        protected string                $name,
        protected string                $namespace,
        protected string                $repository,
        protected ?ProjectRM            $project = null,
        protected ?FieldRMCollection    $fields = null,
        protected ?RelationRMCollection $relations = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(DomainModel $model, bool $full = false): self
    {
        if ($full) {
            $fields = new FieldRMCollection();
            foreach ($model->getFields() as $field) {
                $fields->addItem(FieldRM::hydrateFromModel($field));
            }
            $relations = new RelationRMCollection();
            foreach ($model->getRelations() as $relation) {
                $relations->addItem(RelationRM::hydrateFromModel($relation));
            }
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getSequence(),
                $model->getName(),
                $model->getNamespace(),
                $model->getRepository(),
                ProjectRM::hydrateFromModel($model->getProject()),
                $fields,
                $relations
            );
        } else {
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getSequence(),
                $model->getName(),
                $model->getNamespace(),
                $model->getRepository()
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
        $json->sequence = $this->getSequence();
        $json->name = $this->getName();
        $json->namespace = $this->getNamespace();
        $json->repository = $this->getRepository();
        if (!is_null($this->getProject())) {
            $json->project = $this->getProject();
        }
        if (!is_null($this->getFields())) {
            $json->fields = $this->getFields()->getCollection();
        }
        if (!is_null($this->getRelations())) {
            $json->relations = $this->getRelations()->getCollection();
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

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function getFields(): ?FieldRMCollection
    {
        return $this->fields;
    }

    public function getRelations(): ?RelationRMCollection
    {
        return $this->relations;
    }

    public function getProject(): ?ProjectRM
    {
        return $this->project;
    }
}
