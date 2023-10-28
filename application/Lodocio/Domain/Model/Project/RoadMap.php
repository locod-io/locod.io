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
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Project\RoadMapRepository::class)]
class RoadMap
{
    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 10)]
    private string $code;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;


    // ---------------------------------------------------------------- associations
    /**
     * One-To-Many_Bidirectional
     * One RoadMap has many RoadMapItems.
     */
    #[ORM\OneToMany(mappedBy: "roadMap", targetEntity: "App\Lodocio\Domain\Model\Project\RoadMapItem", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private ?Collection $roadmapitems;

    /**
     * Many-To-One_Unidirectional
     * Many RoadMaps has one DocProject.
     */
    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Project\DocProject", fetch: "EXTRA_LAZY", inversedBy: "roadMaps")]
    #[ORM\JoinColumn(nullable: false)]
    private ?DocProject $docProject;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid       $uuid,
        string     $code,
        string     $name,
        DocProject $docProject,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->code = $code;
        $this->docProject = $docProject;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid       $uuid,
        string     $code,
        string     $name,
        DocProject $docProject,
    ): self {
        return new self(
            $uuid,
            $code,
            $name,
            $docProject
        );
    }

    public function change(
        string $code,
        string $name,
        string $description
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getCode(): string
    {
        return $this->code;
    }

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

    public function getRoadMapItems(): array
    {
        return $this->roadmapitems->getValues();
    }

    public function getDocProject(): DocProject
    {
        return $this->docProject;
    }

}
