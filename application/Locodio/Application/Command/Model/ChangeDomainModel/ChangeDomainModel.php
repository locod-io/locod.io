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

namespace App\Locodio\Application\Command\Model\ChangeDomainModel;

class ChangeDomainModel
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $id,
        protected int    $moduleId,
        protected string $name,
        protected string $namespace,
        protected string $repository,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->id,
            $json->moduleId,
            $json->name,
            $json->namespace,
            $json->repository,
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
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

    public function getModuleId(): int
    {
        return $this->moduleId;
    }
}
