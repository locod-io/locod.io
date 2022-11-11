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
use App\Locodio\Domain\Model\Model\Enum;

class EnumRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                    $id,
        protected string                 $uuid,
        protected string                 $name,
        protected string                 $nameSpace,
        protected EnumOptionRMCollection $options,
        protected DomainModelRM          $domainModelRM,
        protected ?ProjectRM             $project = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Enum $model, bool $full = false): self
    {
        $options = new EnumOptionRMCollection();
        foreach ($model->getOptions() as $option) {
            $options->addItem(EnumOptionRM::hydrateFromModel($option));
        }

        if ($full) {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getNamespace(),
                $options,
                DomainModelRM::hydrateFromModel($model->getDomainModel()),
                ProjectRM::hydrateFromModel($model->getProject())
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getNamespace(),
                $options,
                DomainModelRM::hydrateFromModel($model->getDomainModel())
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
        $json->name = $this->getName();
        $json->namespace = $this->getNameSpace();
        $json->options = $this->getOptions()->getCollection();
        $json->domainModel = $this->getDomainModelRM();
        if (!is_null($this->getProject())) {
            $json->project = $this->getProject();
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

    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }

    public function getOptions(): EnumOptionRMCollection
    {
        return $this->options;
    }

    public function getDomainModelRM(): DomainModelRM
    {
        return $this->domainModelRM;
    }

    public function getProject(): ?ProjectRM
    {
        return $this->project;
    }
}
