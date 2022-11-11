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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\EnumRepository::class)]
class Enum
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
    private string $name;

    #[ORM\Column(length: 191)]
    private string $namespace;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY", inversedBy: "enums")]
    #[ORM\JoinColumn(nullable: false)]
    private DomainModel $domainModel;

    #[ORM\OneToMany(mappedBy: "enum", targetEntity: "App\Locodio\Domain\Model\Model\EnumOption", fetch: "EXTRA_LAZY")]
    #[ORM\OrderBy(['sequence' => 'ASC'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $options;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy: "enums")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor & assertion
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(
        Project     $project,
        Uuid        $uuid,
        DomainModel $domainModel,
        string      $name,
    ) {
        $this->project = $project;
        $this->uuid = $uuid;
        $this->domainModel = $domainModel;
        $this->name = $name;
        $this->namespace = '';
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // ———————————————————————————————————————————————————————————————————————————————————————

    public static function make(
        Project     $project,
        Uuid        $uuid,
        DomainModel $domainModel,
        string      $name,
    ): self {
        return new self(
            $project,
            $uuid,
            $domainModel,
            $name,
        );
    }

    public function change(
        DomainModel $domainModel,
        string      $name,
        string      $namespace
    ): void {
        $this->domainModel = $domainModel;
        $this->name = $name;
        $this->namespace = $namespace;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getDomainModel(): DomainModel
    {
        return $this->domainModel;
    }

    /**
     * @return EnumOption[]
     */
    public function getOptions(): array
    {
        return $this->options->getValues();
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
