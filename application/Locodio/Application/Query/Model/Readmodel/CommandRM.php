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
use App\Locodio\Domain\Model\Model\Command;

class CommandRM implements \JsonSerializable, DocumentationItemInterface
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int           $id,
        protected string        $uuid,
        protected int           $artefactId,
        protected string        $name,
        protected string        $nameSpace,
        protected array         $mapping,
        protected DomainModelRM $domainModelRM,
        protected DocumentorRM  $documentor,
        protected ?ProjectRM    $project = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Command $model, bool $full = false): self
    {
        if ($full) {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getArtefactId(),
                $model->getName(),
                $model->getNamespace(),
                $model->getMapping(),
                DomainModelRM::hydrateFromModel($model->getDomainModel(), true),
                DocumentorRM::hydrateFromModel($model->getDocumentor(), true),
                ProjectRM::hydrateFromModel($model->getProject()),
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getArtefactId(),
                $model->getName(),
                $model->getNamespace(),
                $model->getMapping(),
                DomainModelRM::hydrateFromModel($model->getDomainModel(), true),
                DocumentorRM::hydrateFromModel($model->getDocumentor()),
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
        $json->artefactId = 'C-'.$this->getArtefactId();
        $json->name = $this->getName();
        $json->namespace = $this->getNameSpace();
        $json->mapping = $this->getMapping();
        $json->domainModel = $this->getDomainModelRM();
        $json->documentor = $this->getDocumentor();
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

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function getDomainModelRM(): DomainModelRM
    {
        return $this->domainModelRM;
    }

    public function getProject(): ?ProjectRM
    {
        return $this->project;
    }

    public function getDocumentor(): DocumentorRM
    {
        return $this->documentor;
    }

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }
}
