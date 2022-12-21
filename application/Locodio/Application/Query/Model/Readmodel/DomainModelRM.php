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

class DomainModelRM implements \JsonSerializable, DocumentationItemInterface
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                      $id,
        protected string                   $uuid,
        protected int                      $sequence,
        protected string                   $name,
        protected string                   $namespace,
        protected string                   $repository,
        protected DocumentorRM             $documentor,
        protected ?ModuleRM                $module,
        protected ?ProjectRM               $project = null,
        protected ?AttributeRMCollection   $attributes = null,
        protected ?AssociationRMCollection $associations = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(DomainModel $model, bool $full = false): self
    {
        $moduleRM = null;
        if (!is_null($model->getModule())) {
            $moduleRM = ModuleRM::hydrateFromModel($model->getModule());
        }

        if ($full) {
            $attributes = new AttributeRMCollection();
            foreach ($model->getAttributes() as $attribute) {
                $attributes->addItem(AttributeRM::hydrateFromModel($attribute));
            }
            $associations = new AssociationRMCollection();
            foreach ($model->getAssociations() as $association) {
                $associations->addItem(AssociationRM::hydrateFromModel($association));
            }
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getSequence(),
                $model->getName(),
                $model->getNamespace(),
                $model->getRepository(),
                DocumentorRM::hydrateFromModel($model->getDocumentor(), true),
                $moduleRM,
                ProjectRM::hydrateFromModel($model->getProject()),
                $attributes,
                $associations
            );
        } else {
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getSequence(),
                $model->getName(),
                $model->getNamespace(),
                $model->getRepository(),
                DocumentorRM::hydrateFromModel($model->getDocumentor()),
                $moduleRM,
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
        $json->documentor = $this->getDocumentor();
        $json->module = $this->getModule();

        if (!is_null($this->getProject())) {
            $json->project = $this->getProject();
        }
        if (!is_null($this->getAttributes())) {
            $json->attributes = $this->getAttributes()->getCollection();
        }
        if (!is_null($this->getAssociations())) {
            $json->associations = $this->getAssociations()->getCollection();
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

    public function getAttributes(): ?AttributeRMCollection
    {
        return $this->attributes;
    }

    public function getAssociations(): ?AssociationRMCollection
    {
        return $this->associations;
    }

    public function getProject(): ?ProjectRM
    {
        return $this->project;
    }

    public function getModule(): ?ModuleRM
    {
        return $this->module;
    }

    public function getDocumentor(): DocumentorRM
    {
        return $this->documentor;
    }
}
