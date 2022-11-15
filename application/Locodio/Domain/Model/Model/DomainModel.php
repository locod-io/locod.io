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

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 191)]
    private string $namespace;

    #[ORM\Column(length: 191)]
    private string $repository;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "App\Locodio\Domain\Model\Model\Field", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $fields;

    #[ORM\OneToMany(mappedBy: "domainModel", targetEntity: "App\Locodio\Domain\Model\Model\Relation", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $relations;

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

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy:"domainModels")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

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
        $this->fields = new ArrayCollection();
        $this->relations = new ArrayCollection();
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
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields->getValues();
    }

    /**
     * @return Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations->getValues();
    }

    /**
     * @return ReadModel[]
     */
    public function getReadModels(): array
    {
        return $this->readModels->getValues();
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
}
