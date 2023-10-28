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

namespace App\Lodocio\Domain\Model\Project;

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
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Project\RoadMapItemRepository::class)]
class RoadMapItem
{
    // -------------------------------------------------------------- attributes
    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    // ---------------------------------------------------------------- associations
    /**
     * Many-To-One_Unidirectional
     * Many RoadMapItems has one RoadMap.
     */
    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Project\RoadMap", fetch: "EXTRA_LAZY", inversedBy: "roadMapItems")]
    #[ORM\JoinColumn(nullable: false)]
    private RoadMap $roadMap;

    /**
     * Many-To-One_Unidirectional
     * Many RoadMapItems has one Release.
     */
    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Project\Release", fetch: "EXTRA_LAZY", inversedBy: "roadMapItems")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Release $release;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid    $uuid,
        string  $name,
        string  $description,
        RoadMap $roadMap
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->description = $description;
        $this->roadMap = $roadMap;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid    $uuid,
        string  $name,
        string  $description,
        RoadMap $roadMap
    ): self {
        return new self(
            $uuid,
            $name,
            $description,
            $roadMap
        );
    }

    public function change(
        string $name,
        string $description,
    ): void {
        $this->name = $name;
        $this->description = $description;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getRoadMap(): RoadMap
    {
        return $this->roadMap;
    }

    public function getRelease(): ?Release
    {
        return $this->release;
    }

}
