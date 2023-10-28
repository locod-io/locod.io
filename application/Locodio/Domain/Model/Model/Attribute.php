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

use App\Locodio\Domain\Model\Common\ArtefactEntity;
use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\AttributeRepository::class)]
class Attribute
{
    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use ArtefactEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 191)]
    private string $type;

    #[ORM\Column]
    private int $length;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isIdentifier;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isRequired;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isUnique;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isMake;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isChange;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY", inversedBy: "attributes")]
    #[ORM\JoinColumn(nullable: false)]
    private DomainModel $domainModel;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\Enum", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Enum $enum;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(
        DomainModel   $domainModel,
        Uuid          $uuid,
        string        $name,
        int           $length,
        AttributeType $type,
        bool          $identifier,
        bool          $required,
        bool          $unique,
        bool          $make,
        bool          $change
    ) {
        $this->domainModel = $domainModel;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->length = $length;
        $this->type = $type->value;
        $this->isIdentifier = $identifier;
        $this->isRequired = $required;
        $this->isUnique = $unique;
        $this->isMake = $make;
        $this->isChange = $change;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Make and change
    // ———————————————————————————————————————————————————————————————————————————————————————

    public static function make(
        DomainModel   $domainModel,
        Uuid          $uuid,
        string        $name,
        int           $length,
        AttributeType $type,
        bool          $identifier,
        bool          $required,
        bool          $unique,
        bool          $make,
        bool          $change
    ): self {
        return new self($domainModel, $uuid, $name, $length, $type, $identifier, $required, $unique, $make, $change);
    }

    public function change(
        string        $name,
        int           $length,
        AttributeType $type,
        bool          $identifier,
        bool          $required,
        bool          $unique,
        bool          $make,
        bool          $change
    ): void {
        $this->name = $name;
        $this->length = $length;
        $this->type = $type->value;
        $this->isIdentifier = $identifier;
        $this->isRequired = $required;
        $this->isUnique = $unique;
        $this->isMake = $make;
        $this->isChange = $change;
    }

    public function setEnum(?Enum $enum): void
    {
        $this->enum = $enum;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): AttributeType
    {
        return AttributeType::from($this->type);
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function isIdentifier(): bool
    {
        return $this->isIdentifier;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    public function isMake(): bool
    {
        return $this->isMake;
    }

    public function isChange(): bool
    {
        return $this->isChange;
    }

    public function getDomainModel(): DomainModel
    {
        return $this->domainModel;
    }

    public function getEnum(): ?Enum
    {
        return $this->enum;
    }
}
