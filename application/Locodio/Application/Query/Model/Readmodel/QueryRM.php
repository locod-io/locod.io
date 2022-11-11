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
use App\Locodio\Domain\Model\Model\Query;

class QueryRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int           $id,
        protected string        $uuid,
        protected string        $name,
        protected string        $nameSpace,
        protected array         $mapping,
        protected array         $listing,
        protected array         $detailing,
        protected DomainModelRM $domainModelRM,
        protected ?ProjectRM    $project = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Query $model, bool $full = false): self
    {
        if ($full) {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getNamespace(),
                $model->getMapping(),
                $model->getListing(),
                $model->getDetailing(),
                DomainModelRM::hydrateFromModel($model->getDomainModel(), true),
                ProjectRM::hydrateFromModel($model->getProject())
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getNamespace(),
                $model->getMapping(),
                $model->getListing(),
                $model->getDetailing(),
                DomainModelRM::hydrateFromModel($model->getDomainModel(), true)
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
        $json->mapping = $this->getMapping();
        // $json->listing = $this->getListing();
        // $json->detailing = $this->getDetailing();
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

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function getListing(): array
    {
        return $this->listing;
    }

    public function getDetailing(): array
    {
        return $this->detailing;
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
