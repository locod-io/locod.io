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

use App\Locodio\Domain\Model\Model\Module;

class ModuleRM implements \JsonSerializable, DocumentationItemInterface
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int          $id,
        protected string       $uuid,
        protected string       $name,
        protected string       $namespace,
        protected int          $sequence,
        protected DocumentorRM $documentor,
        protected ?int          $usages = null,
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
        $json->name = $this->getName();
        $json->namespace = $this->getNamespace();
        $json->sequence = $this->getSequence();
        $json->documentor = $this->getDocumentor();
        if (!is_null($this->getUsages())) {
            $json->usages = $this->getUsages();
        }
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Module $model, bool $full = false): self
    {
        if ($full) {
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getName(),
                $model->getNamespace(),
                $model->getSequence(),
                DocumentorRM::hydrateFromModel($model->getDocumentor(), true)
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getName(),
                $model->getNamespace(),
                $model->getSequence(),
                DocumentorRM::hydrateFromModel($model->getDocumentor())
            );
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setUsages(int $usages): void
    {
        $this->usages = $usages;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getDocumentor(): DocumentorRM
    {
        return $this->documentor;
    }

    public function getUsages(): ?int
    {
        return $this->usages;
    }
}
