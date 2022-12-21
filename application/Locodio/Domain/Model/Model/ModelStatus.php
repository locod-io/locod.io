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
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\ModelStatusRepository::class)]
class ModelStatus
{
    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 10)]
    private string $color = '#c7c728';

    #[ORM\Column(options: ["default" => 0])]
    private bool $isStart = false;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isFinal = false;

    #[ORM\Column]
    private ?array $flowIn = [];

    #[ORM\Column]
    private ?array $flowOut = [];

    #[ORM\Column(options: ["default" => 0])]
    private int $x = 0;

    #[ORM\Column(options: ["default" => 0])]
    private int $y = 0;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy: "modelStatus")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Project $project,
        Uuid    $uuid,
        string  $name,
        string  $color,
        bool    $isStart,
        bool    $isFinal,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->color = $color;
        $this->isStart = $isStart;
        $this->isFinal = $isFinal;
        $this->project = $project;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Project $project,
        Uuid    $uuid,
        string  $name,
        string  $color,
        bool    $isStart,
        bool    $isFinal,
    ): self {
        return new self(
            $project,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    public function isStart(): bool
    {
        return $this->isStart;
    }

    public function getFlowIn(): ?array
    {
        return $this->flowIn;
    }

    public function getFlowOut(): ?array
    {
        return $this->flowOut;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other model getters
    // —————————————————————————————————————————————————————————————————————————

    public function getProject(): Project
    {
        return $this->project;
    }
}
