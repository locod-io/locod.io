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

use App\Lodocio\Application\Helper\SlugFunctions;
use App\Lodocio\Domain\Model\Common\ArtefactEntity;
use App\Lodocio\Domain\Model\Common\ChecksumEntity;
use App\Lodocio\Domain\Model\Common\EntityId;
use App\Lodocio\Domain\Model\Common\SequenceEntity;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Tracker\DTO\TrackerStructure;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Tracker\TrackerRepository::class)]
class Tracker
{
    // -------------------------------------------------------------- attributes

    use EntityId;
    use SequenceEntity;
    use ArtefactEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use ProjectDocumentTrait;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 10)]
    private string $color;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column(length: 191)]
    private string $slug;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isPublic = false;

    #[ORM\Column(options: ["default" => 1])]
    private bool $showOnlyFinalNodes = true;

    #[ORM\Column(type: 'json')]
    private array|\stdClass $structure;

    #[ORM\Column]
    private array $relatedTeams = [];

    // ---------------------------------------------------------------- associations

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Project\DocProject", fetch: "EXTRA_LAZY", inversedBy: "trackers")]
    #[ORM\JoinColumn(nullable: false)]
    private DocProject $project;

    #[ORM\OneToMany(mappedBy: "tracker", targetEntity: "App\Lodocio\Domain\Model\Tracker\TrackerNodeStatus", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $trackerNodeStatus;

    #[ORM\OneToMany(mappedBy: "tracker", targetEntity: "App\Lodocio\Domain\Model\Tracker\TrackerNode", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $trackerNodes;

    #[ORM\OneToMany(mappedBy: "tracker", targetEntity: "App\Lodocio\Domain\Model\Tracker\TrackerNodeGroup", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $trackerGroups;

    /**
     * Many-To-Many_Self-referencing
     * Many Trackers have many Trackers.
     */
    #[ORM\ManyToMany(targetEntity: "App\Lodocio\Domain\Model\Tracker\Tracker", mappedBy: "upstream", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $downstream;

    /**
     * Many Trackers have many Trackers.
     */
    #[ORM\ManyToMany(targetEntity: "App\Lodocio\Domain\Model\Tracker\Tracker", inversedBy: "downstream", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $upstream;

    /**
     * One-To-Many_Bidirectional
     * One Tracker has many TrackerFields.
     */
    //    #[ORM\OneToMany(mappedBy: "tracker", targetEntity: "App\Lodocio\Domain\Model\Tracker\TrackerField", fetch: "EXTRA_LAZY")]
    //    #[ORM\JoinColumn(nullable: true)]
    //    #[ORM\OrderBy(["id" => "ASC"])]
    //    private ?Collection $trackerFields;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        DocProject $project,
        Uuid       $uuid,
        string     $name,
        string     $code,
        string     $color,
    ) {
        $this->project = $project;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->code = $code;
        $this->color = $color;
        $this->slug = SlugFunctions::generateRandomSlug();
        $this->isPublic = false;
        $this->showOnlyFinalNodes = true;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        DocProject $project,
        Uuid       $uuid,
        string     $name,
        string     $code,
        string     $color,
    ): self {
        return new self(
            $project,
            $uuid,
            $name,
            $code,
            $color,
        );
    }

    public function change(
        string $name,
        string $code,
        string $color,
        string $description,
        array  $relatedTeams,
        string $slug,
        bool   $isPublic,
        bool   $showOnlyFinalNodes,
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->color = $color;
        $this->description = $description;
        $this->relatedTeams = $relatedTeams;
        $this->slug = SlugFunctions::slugify($slug);
        $this->isPublic = $isPublic;
        $this->showOnlyFinalNodes = $showOnlyFinalNodes;
    }

    public function setStructure(TrackerStructure $structure): void
    {
        $convertedStructure = json_decode(json_encode($structure));
        $this->structure = $convertedStructure;
    }

    public function setRawStructure(array|\stdClass $structure): void
    {
        $convertedStructure = json_decode(json_encode($structure));
        $this->structure = $convertedStructure;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function showOnlyFinalNodes(): bool
    {
        return $this->showOnlyFinalNodes;
    }

    public function getStructure(): array|\stdClass
    {
        return $this->structure;
    }

    public function getRelatedTeams(): array
    {
        return $this->relatedTeams;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getProject(): DocProject
    {
        return $this->project;
    }

    public function getTrackerNodes(): array
    {
        return $this->trackerNodes->getValues();
    }

    public function getTrackerGroups(): array
    {
        return $this->trackerGroups->getValues();
    }

    public function getTrackerNodeStatus(): array
    {
        return $this->trackerNodeStatus->getValues();
    }

    public function getUpstream(): array
    {
        return $this->upstream->getValues();
    }

    public function getDownstream(): array
    {
        return $this->downstream->getValues();
    }

    public function getTrackerFields(): array
    {
        return $this->trackerFields->getValues();
    }

}
