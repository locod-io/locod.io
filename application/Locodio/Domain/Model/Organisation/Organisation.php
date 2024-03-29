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
use App\Lodocio\Application\Helper\SlugFunctions;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
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

    #[ORM\Column(length: 36)]
    private string $icon = '';

    #[ORM\Column(length: 191, options: ["default" => ''])]
    private string $linearApiKey = '';

    #[ORM\Column(length: 191, options: ["default" => ''])]
    private string $figmaApiKey = '';

    #[ORM\ManyToMany(targetEntity: "App\Locodio\Domain\Model\User\User", inversedBy: "organisations")]
    private Collection $users;

    #[ORM\Column(length: 191)]
    private string $slug = '';

    #[ORM\OneToMany(mappedBy: "organisation", targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: "organisation", targetEntity: "App\Lodocio\Domain\Model\Project\DocProject", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $docProjects;

    #[ORM\OneToMany(mappedBy: "organisation", targetEntity: "App\Locodio\Domain\Model\Organisation\OrganisationUser", fetch: "EXTRA_LAZY")]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $organisationUsers;

    #[ORM\OneToMany(mappedBy: "organisation", targetEntity: "App\Locodio\Domain\Model\User\UserInvitationLink", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $userInvitationLinks;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(Uuid $uuid, string $name, string $code)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->code = $code;
        $this->slug = SlugFunctions::generateRandomSlug();
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->docProjects = new ArrayCollection();
    }

    public static function make(Uuid $uuid, string $name, string $code): self
    {
        return new self($uuid, $name, $code);
    }

    public function change(
        string $name,
        string $code,
        string $color,
        string $linearApiKey,
        string $figmaApiKey,
        string $slug,
    ): void {
        $this->name = $name;
        $this->code = $code;
        $this->color = $color;
        $this->slug = $slug;
        $this->linearApiKey = $linearApiKey;
        $this->figmaApiKey = $figmaApiKey;
    }

    public function addUser(User $user): void
    {
        if ($this->users->contains($user)) {
            return;
        }
        $this->users->add($user);
    }

    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Setters
    // ———————————————————————————————————————————————————————————————————————————————————————

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

    public function getDocProjects(): array
    {
        return $this->docProjects->getValues();
    }

    public function getLinearApiKey(): string
    {
        return $this->linearApiKey;
    }

    public function getFigmaApiKey(): string
    {
        return $this->figmaApiKey;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return OrganisationUser[]
     */
    public function getOrganisationUsers(): array
    {
        return $this->organisationUsers->getValues();
    }

}
