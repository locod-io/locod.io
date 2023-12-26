<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Domain\Model\Tracker;

use App\Lodocio\Domain\Model\Common\ArtefactEntity;
use App\Lodocio\Domain\Model\Common\ChecksumEntity;
use App\Lodocio\Domain\Model\Common\EntityId;
use App\Lodocio\Domain\Model\Common\SequenceEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeGroupRepository::class)]
class TrackerNodeGroup
{
    use EntityId;
    use ArtefactEntity;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use ProjectDocumentTrait;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column]
    private int $level;

    #[ORM\Column(length: 50)]
    private string $number;

    #[ORM\Column(options: ["default" => 1])]
    private bool $isOpen = true;

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Tracker\Tracker", fetch: "EXTRA_LAZY", inversedBy: "trackerGroups")]
    #[ORM\JoinColumn(nullable: false)]
    private Tracker $tracker;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Tracker $tracker,
        Uuid    $uuid,
        string  $name,
        int     $level,
        string  $number,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->level = $level;
        $this->tracker = $tracker;
        $this->number = $number;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Tracker $tracker,
        Uuid    $uuid,
        string  $name,
        int     $level,
        string  $number,
    ): self {
        return new self(
            $tracker,
            $uuid,
            $name,
            $level,
            $number,
        );
    }

    public function setName(
        string $name,
    ): void {
        $this->name = $name;
    }

    public function sync(
        int    $level,
        string $number,
        bool   $isOpen,
    ): void {
        $this->level = $level;
        $this->number = $number;
        $this->isOpen = $isOpen;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getTracker(): Tracker
    {
        return $this->tracker;
    }

}
