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
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable ()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\DocumentorRepository::class)]
class Documentor
{
    // -------------------------------------------------------------- attributes

    use EntityId;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 36)]
    private string $type;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?\stdClass $overview = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finalAt = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $finalBy = null;

    // ---------------------------------------------------------------- associations

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\ModelStatus", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    private ModelStatus $status;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid           $uuid,
        DocumentorType $type,
        string         $description,
        ModelStatus    $status,
    ) {
        // -------------------------------------------------- set the attributes
        $this->uuid = $uuid;
        $this->type = $type->value;
        $this->description = $description;

        // ------------------------------------------------ set the associations
        $this->status = $status;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid           $uuid,
        DocumentorType $type,
        string         $description,
        ModelStatus    $status,
    ): self {
        return new self(
            $uuid,
            $type,
            $description,
            $status,
        );
    }

    public function change(
        string      $description,
        ModelStatus $status,
    ): void {
        $this->description = $description;
        $this->status = $status;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setStatus(ModelStatus $status): void
    {
        $this->status = $status;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function setOverview(\stdClass $overview): void
    {
        $this->overview = $overview;
    }

    public function finalize(string $finalBy): void
    {
        $this->finalAt = new \DateTimeImmutable();
        $this->finalBy = $finalBy;
    }

    public function deFinalize(): void
    {
        $this->finalAt = null;
        $this->finalBy = null;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getType(): DocumentorType
    {
        return DocumentorType::from($this->type);
    }

    public function getTypeAsString(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getOverview(): ?\stdClass
    {
        return $this->overview;
    }

    public function getStatus(): ModelStatus
    {
        return $this->status;
    }

    public function getFinalAt(): ?\DateTimeImmutable
    {
        return $this->finalAt;
    }

    public function getFinalBy(): ?string
    {
        return $this->finalBy;
    }
}
