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

namespace App\Lodocio\Domain\Model\Wiki;

use App\Lodocio\Application\Helper\SlugFunctions;
use App\Lodocio\Domain\Model\Common\ArtefactEntity;
use App\Lodocio\Domain\Model\Common\ChecksumEntity;
use App\Lodocio\Domain\Model\Common\EntityId;
use App\Lodocio\Domain\Model\Common\SequenceEntity;
use App\Lodocio\Domain\Model\Project\DocProject;
use App\Lodocio\Domain\Model\Wiki\DTO\WikiStructure;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Wiki\WikiRepository::class)]
class Wiki
{
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

    #[ORM\Column(length: 36)]
    private string $icon = '';

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

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Project\DocProject", fetch: "EXTRA_LAZY", inversedBy: "wikis")]
    #[ORM\JoinColumn(nullable: false)]
    private DocProject $project;

    #[ORM\OneToMany(mappedBy: "wiki", targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiNodeStatus", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $wikiNodeStatus;

    #[ORM\OneToMany(mappedBy: "wiki", targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiNode", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $wikiNodes;

    #[ORM\OneToMany(mappedBy: "wiki", targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiNodeGroup", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $wikiGroups;

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

    // —————————————————————————————————————————————————————————————————————————
    // Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setStructure(WikiStructure $structure): void
    {
        $convertedStructure = json_decode(json_encode($structure));
        $this->structure = $convertedStructure;
    }

    public function setRawStructure(array|\stdClass $structure): void
    {
        $convertedStructure = json_decode(json_encode($structure));
        $this->structure = $convertedStructure;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
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

    public function getWikiNodes(): array
    {
        return $this->wikiNodes->getValues();
    }

    public function getWikiGroups(): array
    {
        return $this->wikiGroups->getValues();
    }

    public function getWikiNodeStatus(): array
    {
        return $this->wikiNodeStatus->getValues();
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

}
