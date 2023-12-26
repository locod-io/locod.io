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

use App\Lodocio\Domain\Model\Common\ArtefactEntity;
use App\Lodocio\Domain\Model\Common\ChecksumEntity;
use App\Lodocio\Domain\Model\Common\EntityId;
use App\Lodocio\Domain\Model\Common\SequenceEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Wiki\WikiNodeRepository::class)]
class WikiNode
{
    use EntityId;
    use SequenceEntity;
    use ArtefactEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use ProjectDocumentTrait;

    #[ORM\Column]
    private int $level = 0;

    #[ORM\Column(length: 50)]
    private string $number = '';
    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(options: ["default" => 1])]
    private bool $isOpen = true;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finalAt = null;

    #[ORM\Column(length: 191)]
    private string $finalBy = '';

    #[ORM\Column]
    private array $relatedIssues = [];

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Wiki\Wiki", fetch: "EXTRA_LAZY", inversedBy: "wikiNodes")]
    #[ORM\JoinColumn(nullable: false)]
    private Wiki $wiki;

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiNodeStatus", fetch: "EXTRA_LAZY", inversedBy: "wikiNodes")]
    #[ORM\JoinColumn(nullable: false)]
    private WikiNodeStatus $wikiNodeStatus;

    #[ORM\OneToMany(mappedBy: "wikiNode", targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiNodeFile", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $wikiNodeFiles;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Wiki           $wiki,
        Uuid           $uuid,
        WikiNodeStatus $status,
        string         $name,
        int            $level,
        string         $number,
    ) {
        $this->wiki = $wiki;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->wikiNodeStatus = $status;
        $this->description = "";
        $this->level = $level;
        $this->number = $number;
        $this->relatedIssues = [];
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Wiki           $wiki,
        Uuid           $uuid,
        WikiNodeStatus $status,
        string         $name,
        int            $level,
        string         $number
    ): self {
        return new self(
            $wiki,
            $uuid,
            $status,
            $name,
            $level,
            $number
        );
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setRelatedIssues(array $relatedIssues): void
    {
        $this->relatedIssues = $relatedIssues;
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

    public function setStatus(WikiNodeStatus $status): void
    {
        $this->wikiNodeStatus = $status;
    }

    public function finalize(string $finalBy): void
    {
        $this->finalAt = new \DateTimeImmutable();
        $this->finalBy = $finalBy;
    }

    public function deFinalize(): void
    {
        $this->finalAt = null;
        $this->finalBy = '';
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

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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

    public function getWiki(): ?Wiki
    {
        return $this->wiki;
    }

    public function getWikiNodeStatus(): ?WikiNodeStatus
    {
        return $this->wikiNodeStatus;
    }

    public function getRelatedIssues(): array
    {
        return $this->relatedIssues;
    }

    public function getFinalAt(): ?\DateTimeImmutable
    {
        return $this->finalAt;
    }

    public function getFinalBy(): string
    {
        return $this->finalBy;
    }

    public function getWikiNodeFiles(): array
    {
        return $this->wikiNodeFiles->getValues();
    }
}
