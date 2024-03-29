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

namespace App\Locodio\Domain\Model\Organisation;

use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use App\Locodio\Domain\Model\Model\ModelSettings;
use App\Lodocio\Application\Helper\SlugFunctions;
use App\Lodocio\Domain\Model\Project\DocProject;
use Doctrine\Common\Collections\Collection;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\ProjectRepository::class)]
class Project
{
    use TimestampableEntity;
    use BlameableEntity;
    use EntityId;
    use SequenceEntity;
    use ChecksumEntity;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 10)]
    private string $code;

    #[ORM\Column(length: 10)]
    private ?string $color = '#10a343';

    #[ORM\Column(length: 36)]
    private string $icon = '';

    #[ORM\Column(length: 191)]
    private ?string $domainLayer = 'App\Domain';

    #[ORM\Column(length: 191)]
    private ?string $applicationLayer = 'App\Application';

    #[ORM\Column(length: 191)]
    private ?string $infrastructureLayer = 'App\Infrastructure';

    #[ORM\Column(length: 191)]
    private ?string $logo = '';

    #[ORM\Column(length: 191)]
    private string $slug = '';

    #[ORM\Column]
    private array $relatedRoadmaps = [];

    #[ORM\Column]
    private array $relatedProjects = [];

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column(length: 191)]
    private string $gitRepository = '';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\OneToOne(mappedBy: "project", targetEntity: "App\Lodocio\Domain\Model\Project\DocProject", fetch: "EXTRA_LAZY")]
    private ?DocProject $docProject;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Organisation", fetch: "EXTRA_LAZY", inversedBy: "projects")]
    #[ORM\JoinColumn(nullable: false)]
    private Organisation $organisation;

    #[ORM\OneToOne(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\ModelSettings", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    private ?ModelSettings $modelSettings = null;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\DomainModel", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $domainModels;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\Enum", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $enums;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\Query", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $queries;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\Command", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $commands;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\Template", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $templates;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\Module", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: "App\Locodio\Domain\Model\Model\ModelStatus", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $modelStatus;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    /**
     * @throws \Exception
     */
    private function __construct(Uuid $uuid, string $name, string $code, Organisation $organisation)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->code = $code;
        $this->organisation = $organisation;
        $this->docProject = null;
        $this->slug = SlugFunctions::generateRandomSlug();
        $this->relatedProjects = [];
        $this->relatedRoadmaps = [];
        $this->description = '';
    }

    /**
     * @throws \Exception
     */
    public static function make(Uuid $uuid, string $name, string $code, Organisation $organisation): self
    {
        return new self($uuid, $name, $code, $organisation);
    }

    public function change(
        string $name,
        string $code,
        string $color,
        string $slug,
        string $gitRepository,
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->color = $color;
        $this->slug = $slug;
        $this->gitRepository = $gitRepository;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Setters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function setLayers(string $domain, string $application, string $infrastructure): void
    {
        $this->domainLayer = $domain;
        $this->applicationLayer = $application;
        $this->infrastructureLayer = $infrastructure;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    public function setModelSettings(ModelSettings $modelSettings): void
    {
        $this->modelSettings = $modelSettings;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setRelatedProjects(array $relatedProjects): void
    {
        $this->relatedProjects = $relatedProjects;
    }

    public function setRelatedRoadMaps(array $relatedRoadMaps): void
    {
        $this->relatedRoadmaps = $relatedRoadMaps;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function getTemplates(): array
    {
        return $this->templates->getValues();
    }

    public function getDomainModels(): array
    {
        return $this->domainModels->getValues();
    }

    public function getEnums(): array
    {
        return $this->enums->getValues();
    }

    public function getQueries(): array
    {
        return $this->queries->getValues();
    }

    public function getCommands(): array
    {
        return $this->commands->getValues();
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getDomainLayer(): ?string
    {
        return $this->domainLayer;
    }

    public function getApplicationLayer(): ?string
    {
        return $this->applicationLayer;
    }

    public function getInfrastructureLayer(): ?string
    {
        return $this->infrastructureLayer;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function getModules(): array
    {
        return $this->modules->getValues();
    }

    public function getModelStatus(): array
    {
        return $this->modelStatus->getValues();
    }

    public function getModelSettings(): ?ModelSettings
    {
        return $this->modelSettings;
    }

    public function getDocProject(): ?DocProject
    {
        return $this->docProject;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getRelatedProjects(): array
    {
        return $this->relatedProjects;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRelatedRoadmaps(): array
    {
        return $this->relatedRoadmaps;
    }

    public function getGitRepository(): string
    {
        return $this->gitRepository;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }


}
