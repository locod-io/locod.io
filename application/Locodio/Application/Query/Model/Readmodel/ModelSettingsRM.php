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

use App\Locodio\Domain\Model\Model\ModelSettings;

class ModelSettingsRM implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int $id,
        protected string $uuid,
        protected string $domainLayer,
        protected string $applicationLayer,
        protected string $infrastructureLayer,
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
        $json->domainLayer = $this->getDomainLayer();
        $json->applicationLayer = $this->getApplicationLayer();
        $json->infrastructureLayer = $this->getInfrastructureLayer();

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(ModelSettings $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getDomainLayer(),
            $model->getApplicationLayer(),
            $model->getInfrastructureLayer(),
        );
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

    public function getDomainLayer(): string
    {
        return $this->domainLayer;
    }

    public function getApplicationLayer(): string
    {
        return $this->applicationLayer;
    }

    public function getInfrastructureLayer(): string
    {
        return $this->infrastructureLayer;
    }
}
