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

namespace App\Locodio\Application\Command\Model\ChangeModelSettings;

class ChangeModelSettings
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $projectId,
        protected int    $id,
        protected string $domainLayer,
        protected string $applicationLayer,
        protected string $infrastructureLayer,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->projectId,
            $json->id,
            $json->domainLayer,
            $json->applicationLayer,
            $json->infrastructureLayer,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getId(): int
    {
        return $this->id;
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
