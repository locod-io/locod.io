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
use Assert\Assertion;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\CommandRepository::class)]
class Command
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

    #[ORM\Column(type: "json", nullable: true)]
    private array $mapping;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY", inversedBy: "commands")]
    #[ORM\JoinColumn(nullable: false)]
    private DomainModel $domainModel;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy: "commands")]
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
        $this->mapping = [];
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
        string      $namespace,
        array       $mapping
    ): void {
        $this->domainModel = $domainModel;
        $this->name = $name;
        $this->namespace = $namespace;
        $this->mapping = $mapping;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Property getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Other models getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getDomainModel(): DomainModel
    {
        return $this->domainModel;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
