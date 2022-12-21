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
use App\Locodio\Domain\Model\Common\DocumentorEntity;
use App\Locodio\Domain\Model\Common\DocumentorInterface;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\ModuleRepository::class)]
class Module implements DocumentorInterface
{
    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use DocumentorEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 191)]
    private string $namespace;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy: "modules")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\OneToMany(mappedBy: "module", targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private ?Collection $domainModels;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Project $project,
        Uuid    $uuid,
        string  $name,
        string  $namespace,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->namespace = $namespace;
        $this->project = $project;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Project $project,
        Uuid    $uuid,
        string  $name,
        string  $namespace,
    ): self {
        return new self(
            $project,
            $uuid,
            $name,
            $namespace,
        );
    }

    public function change(
        string $name,
        string $namespace,
    ): void {
        $this->name = $name;
        $this->namespace = $namespace;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getDomainModels(): array
    {
        return $this->domainModels->getValues();
    }
}
