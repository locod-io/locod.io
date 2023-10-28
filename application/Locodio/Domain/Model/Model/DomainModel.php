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
use App\Locodio\Domain\Model\Common\DocumentorEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\DomainModelRepository::class)]
class DomainModel
{
    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use DocumentorEntity;
    use ArtefactEntity;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 191)]
    private string $namespace;

    #[ORM\Column(length: 191)]
    private string $repository;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "Attribute", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $attributes;

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "Association", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $associations;

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "App\Locodio\Domain\Model\Model\Enum", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $enums;

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "App\Locodio\Domain\Model\Model\Query", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $queries;

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "App\Locodio\Domain\Model\Model\Command", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $commands;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy: "domainModels")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\Module", fetch: "EXTRA_LAZY", inversedBy: "domainModels")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Module $module = null;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(
        Project $project,
        Uuid    $uuid,
        string  $name,
        string  $namespace,
        string  $repository
    ) {
        $this->project = $project;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->namespace = $namespace;
        $this->repository = $repository;
        $this->attributes = new ArrayCollection();
        $this->associations = new ArrayCollection();
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // ———————————————————————————————————————————————————————————————————————————————————————

    public static function make(
        Project $project,
        Uuid    $uuid,
        string  $name
    ): self {
        return new self($project, $uuid, $name, '', '');
    }

    public function change(
        string $name,
        string $namespace,
        string $repository
    ): void {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->repository = $repository;
    }

    public function setModule(Module $module): void
    {
        $this->module = $module;
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

    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes->getValues();
    }

    /**
     * @return Association[]
     */
    public function getAssociations(): array
    {
        return $this->associations->getValues();
    }

    /**
     * @return Enum[]
     */
    public function getEnums(): array
    {
        return $this->enums->getValues();
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array
    {
        return $this->commands->getValues();
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries->getValues();
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }
}
