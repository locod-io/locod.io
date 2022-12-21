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

namespace App\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass:\App\Locodio\Infrastructure\Database\ModelSettingsRepository::class)]
class ModelSettings
{
    use EntityId;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $domainLayer;

    #[ORM\Column(length: 191)]
    private string $applicationLayer;

    #[ORM\Column(length: 191)]
    private string $infrastructureLayer;

    #[ORM\OneToOne(inversedBy: "modelSettings", targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Project $project,
        Uuid $uuid,
        string $domainLayer,
        string $applicationLayer,
        string $infrastructureLayer,
    ) {
        $this->uuid = $uuid;
        $this->domainLayer = $domainLayer;
        $this->applicationLayer = $applicationLayer;
        $this->infrastructureLayer = $infrastructureLayer;
        $this->project = $project;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Project $project,
        Uuid $uuid,
        string $domainLayer,
        string $applicationLayer,
        string $infrastructureLayer,
    ): self {
        return new self(
            $project,
            $uuid,
            $domainLayer,
            $applicationLayer,
            $infrastructureLayer,
        );
    }

    public function change(
        string $domainLayer,
        string $applicationLayer,
        string $infrastructureLayer,
    ): void {
        $this->domainLayer = $domainLayer;
        $this->applicationLayer = $applicationLayer;
        $this->infrastructureLayer = $infrastructureLayer;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getUuidAsString(): string
    {
        return $this->uuid->toRfc4122();
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

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getProject(): Project
    {
        return $this->project;
    }
}
