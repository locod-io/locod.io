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
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Project\ReleaseRepository::class)]
class Release
{
    // -------------------------------------------------------------- attributes
    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 191)]
    private string $version;

    #[ORM\Column(type: 'text')]
    private string $description;


    // ---------------------------------------------------------------- associations
    /**
     * Many-To-One_Unidirectional
     * Many Releases has one DocProject.
     */
    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Project\DocProject", fetch: "EXTRA_LAZY", inversedBy: "releases")]
    #[ORM\JoinColumn(nullable: false)]
    private ?DocProject $docProject;

    /**
     * One-To-Many_Bidirectional
     * One Release has many RoadMapItems.
     */
    #[ORM\OneToMany(mappedBy: "release", targetEntity: "App\Lodocio\Domain\Model\Project\RoadMapItem", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private ?Collection $roadmapitems;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid       $uuid,
        string     $name,
        string     $version,
        DocProject $docProject,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->version = $version;
        $this->docProject = $docProject;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid       $uuid,
        string     $name,
        string     $version,
        DocProject $docProject,
    ): self {
        return new self(
            $uuid,
            $name,
            $version,
            $docProject
        );
    }

    public function change(
        string $name,
        string $version,
        string $description,
    ): void {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getDocProject(): DocProject
    {
        return $this->docProject;
    }

    public function getRoadMapItems(): array
    {
        return $this->roadmapitems->getValues();
    }

}
