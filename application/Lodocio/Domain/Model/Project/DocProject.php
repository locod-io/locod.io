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

namespace App\Lodocio\Domain\Model\Project;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Lodocio\Domain\Model\Common\ChecksumEntity;
use App\Lodocio\Domain\Model\Common\EntityId;
use App\Lodocio\Domain\Model\Common\SequenceEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Project\DocProjectRepository::class)]
class DocProject
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

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\OneToOne(inversedBy: "docProject", targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Organisation", fetch: "EXTRA_LAZY", inversedBy: "docProjects")]
    #[ORM\JoinColumn(nullable: false)]
    private Organisation $organisation;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(Uuid $uuid, string $name, string $code, Organisation $organisation, Project $project)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->code = $code;
        $this->organisation = $organisation;
        $this->project = $project;
    }

    public static function make(Uuid $uuid, string $name, string $code, Organisation $organisation, Project $project): self
    {
        return new self($uuid, $name, $code, $organisation, $project);
    }

    public function change(string $name, string $code, string $color): void
    {
        $this->name = $name;
        $this->code = $code;
        $this->color = $color;
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

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}
