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
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Wiki\WikiNodeStatusRepository::class)]
class WikiNodeStatus
{
    use EntityId;
    use SequenceEntity;
    use ArtefactEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 10)]
    private string $color;

    #[ORM\Column(options: ["default" => 0])]
    private ?bool $isStart = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?bool $isFinal = null;

    #[ORM\Column]
    private array $flowIn = [];

    #[ORM\Column]
    private array $flowOut = [];

    #[ORM\Column]
    private int $x = 0;

    #[ORM\Column]
    private int $y = 0;

    #[ORM\OneToMany(mappedBy: "wikiNodeStatus", targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiNode", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private ?Collection $wikiNodes;

    #[ORM\ManyToOne(targetEntity: "App\Lodocio\Domain\Model\Wiki\Wiki", fetch: "EXTRA_LAZY", inversedBy: "wikiNodeStatus")]
    #[ORM\JoinColumn(nullable: false)]
    private Wiki $wiki;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Wiki   $wiki,
        Uuid   $uuid,
        string $name,
        string $color,
        bool   $isStart,
        bool   $isFinal,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->color = $color;
        $this->isStart = $isStart;
        $this->isFinal = $isFinal;
        $this->wiki = $wiki;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Wiki   $wiki,
        Uuid   $uuid,
        string $name,
        string $color,
        bool   $isStart,
        bool   $isFinal,
    ): self {
        return new self(
            $wiki,
            $uuid,
            $name,
            $color,
            $isStart,
            $isFinal,
        );
    }

    public function change(
        string $name,
        string $color,
        bool   $isStart,
        bool   $isFinal,
    ): void {
        $this->name = $name;
        $this->color = $color;
        $this->isStart = $isStart;
        $this->isFinal = $isFinal;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other Setters
    // —————————————————————————————————————————————————————————————————————————

    public function deFinalize(): void
    {
        $this->isFinal = false;
    }

    public function deStarterize(): void
    {
        $this->isStart = false;
    }

    public function setWorkflow(\stdClass $position, array $flowIn, array $flowOut): void
    {
        $this->x = intval($position->x);
        $this->y = intval($position->y);
        $this->flowIn = $flowIn;
        $this->flowOut = $flowOut;
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

    public function getColor(): string
    {
        return $this->color;
    }

    public function isStart(): bool
    {
        return $this->isStart;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    public function getFlowIn(): ?array
    {
        return $this->flowIn;
    }

    public function getFlowOut(): ?array
    {
        return $this->flowOut;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getWikiNodes(): array
    {
        return $this->wikiNodes->getValues();
    }

    public function getWiki(): Wiki
    {
        return $this->wiki;
    }

}
