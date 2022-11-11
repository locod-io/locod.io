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
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\RelationRepository::class)]
class Relation
{
    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $typeField;

    #[ORM\Column(length: 191)]
    private ?string $mappedBy;

    #[ORM\Column(length: 191)]
    private ?string $inversedBy;

    #[ORM\Column(length: 191)]
    private string $fetchField;

    #[ORM\Column(length: 191)]
    private string $orderByField;

    #[ORM\Column(length: 191)]
    private string $orderDirection;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isMake;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isChange;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isRequired;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY", inversedBy: "relations")]
    #[ORM\JoinColumn(nullable: false)]
    private DomainModel $domainModel;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private DomainModel $targetDomainModel;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor & assertion
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(
        DomainModel  $domainModel,
        Uuid         $uuid,
        RelationType $type,
        string       $mappedBy,
        string       $inversedBy,
        FetchType    $fetch,
        string       $orderBy,
        OrderType    $orderDirection,
        DomainModel  $targetDomainModel
    ) {
        $this->domainModel = $domainModel;
        $this->uuid = $uuid;
        $this->typeField = $type->value;
        $this->mappedBy = $mappedBy;
        $this->inversedBy = $inversedBy;
        $this->fetchField = $fetch->value;
        $this->orderByField = $orderBy;
        $this->orderDirection = $orderDirection->value;
        $this->targetDomainModel = $targetDomainModel;
        $this->isMake = false;
        $this->isChange = false;
        $this->isRequired = false;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // ———————————————————————————————————————————————————————————————————————————————————————

    public static function make(
        DomainModel  $domainModel,
        Uuid         $uuid,
        RelationType $type,
        string       $mappedBy,
        string       $inversedBy,
        FetchType    $fetch,
        string       $orderBy,
        OrderType    $orderDirection,
        DomainModel  $targetDomainModel
    ): self {
        return new self(
            $domainModel,
            $uuid,
            $type,
            $mappedBy,
            $inversedBy,
            $fetch,
            $orderBy,
            $orderDirection,
            $targetDomainModel
        );
    }

    public function change(
        RelationType $type,
        string       $mappedBy,
        string       $inversedBy,
        FetchType    $fetch,
        string       $orderBy,
        OrderType    $orderDirection,
        DomainModel  $targetDomainModel,
        bool         $isMake,
        bool         $isChange,
        bool         $isRequired,
    ): void {
        $this->typeField = $type->value;
        $this->mappedBy = $mappedBy;
        $this->inversedBy = $inversedBy;
        $this->fetchField = $fetch->value;
        $this->orderByField = $orderBy;
        $this->orderDirection = $orderDirection->value;
        $this->targetDomainModel = $targetDomainModel;
        $this->isMake = $isMake;
        $this->isChange = $isChange;
        $this->isRequired = $isRequired;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getType(): RelationType
    {
        return RelationType::from($this->typeField);
    }

    public function getMappedBy(): ?string
    {
        return $this->mappedBy;
    }

    public function getInversedBy(): ?string
    {
        return $this->inversedBy;
    }

    public function getFetch(): FetchType
    {
        return FetchType::from($this->fetchField);
    }

    public function getOrderBy(): string
    {
        return $this->orderByField;
    }

    public function getOrderDirection(): OrderType
    {
        return OrderType::from($this->orderDirection);
    }

    public function getDomainModel(): DomainModel
    {
        return $this->domainModel;
    }

    public function getTargetDomainModel(): DomainModel
    {
        return $this->targetDomainModel;
    }

    public function isMake(): bool
    {
        return $this->isMake;
    }

    public function isChange(): bool
    {
        return $this->isChange;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
