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
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Tracker\TrackerNodeFileRepository::class)]
class TrackerNodeFile
{
    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use ArtefactEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 191)]
    private string $originalFileName;

    #[ORM\Column(length: 191)]
    private string $srcPath;

    #[ORM\Column(length: 191)]
    private string $previewPath;

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Tracker\TrackerNode", fetch: "EXTRA_LAZY", inversedBy: "trackerNodeFiles")]
    #[ORM\JoinColumn(nullable: false)]
    private TrackerNode $trackerNode;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        TrackerNode $trackerNode,
        Uuid        $uuid,
        int         $artefactId,
        int         $sequence,
        string      $name,
        string      $originalFileName,
        string      $srcPath,
        string      $previewPath,
    ) {
        $this->trackerNode = $trackerNode;
        $this->uuid = $uuid;
        $this->sequence = $sequence;
        $this->artefactId = $artefactId;
        $this->name = $name;
        $this->originalFileName = $originalFileName;
        $this->srcPath = $srcPath;
        $this->previewPath = $previewPath;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        TrackerNode $trackerNode,
        Uuid        $uuid,
        int         $artefactId,
        int         $sequence,
        string      $name,
        string      $originalFileName,
        string      $srcPath,
        string      $previewPath
    ): self {
        return new self(
            $trackerNode,
            $uuid,
            $artefactId,
            $sequence,
            $name,
            $originalFileName,
            $srcPath,
            $previewPath,
        );
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getSrcPath(): string
    {
        return $this->srcPath;
    }

    public function getPreviewPath(): string
    {
        return $this->previewPath;
    }

    public function getTrackerNode(): ?TrackerNode
    {
        return $this->trackerNode;
    }

}
