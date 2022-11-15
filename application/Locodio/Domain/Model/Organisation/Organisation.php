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
use App\Locodio\Domain\Model\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\OrganisationRepository::class)]
class Organisation
{
    use TimestampableEntity;
    use BlameableEntity;
    use EntityId;
    use ChecksumEntity;
    use SequenceEntity;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 10)]
    private string $code;

    #[ORM\Column(length: 10)]
    private ?string $color = '#10a343';

    #[ORM\ManyToMany(targetEntity: "App\Locodio\Domain\Model\User\User", inversedBy: "organisations")]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: "organisation", targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $projects;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(Uuid $uuid, string $name, string $code)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->code = $code;
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public static function make(Uuid $uuid, string $name, string $code): self
    {
        return new self($uuid, $name, $code);
    }

    public function change(string $name, string $code, string $color): void
    {
        $this->name = $name;
        $this->code = $code;
        $this->color = $color;
    }

    public function addUser(User $user): void
    {
        $this->users->add($user);
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getProjects(): array
    {
        return $this->projects->getValues();
    }

    public function getUsers(): array
    {
        return $this->users->getValues();
    }
}
