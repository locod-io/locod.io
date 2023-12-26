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

namespace App\Locodio\Domain\Model\User;

use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use Assert\Assertion;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    use EntityId;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 180)]
    private ?string $firstname = null;

    #[ORM\Column(length: 180)]
    private ?string $lastname = null;

    #[ORM\Column(length: 10)]
    private ?string $color = '#D00E6B';

    #[ORM\Column(length: 36, options: ["default" => InterfaceTheme::LIGHT->value])]
    private string $theme = InterfaceTheme::LIGHT->value;

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    // -- sso fields --

    #[ORM\Column(length: 48, options: ["default" => ""])]
    private string $userId = '';

    #[ORM\Column(length: 48, options: ["default" => "system"])]
    private string $provider = 'system';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToMany(targetEntity: "App\Locodio\Domain\Model\Organisation\Organisation", mappedBy: "users")]
    #[ORM\OrderBy(["sequence" => "ASC"])]
    private Collection $organisations;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Locodio\Domain\Model\Organisation\OrganisationUser", fetch: "EAGER")]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $organisationUsers;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Locodio\Domain\Model\Model\MasterTemplate", fetch: "EXTRA_LAZY")]
    #[ORM\OrderBy(['sequence' => 'ASC'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $masterTemplates;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid   $uuid,
        string $email,
        string $firstname,
        string $lastname,
        array  $roles,
    ) {
        $this->uuid = $uuid;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->roles = $roles;
        $this->organisations = new ArrayCollection();
        $this->organisationUsers = new ArrayCollection();
    }

    public static function make(
        Uuid   $uuid,
        string $email,
        string $firstname,
        string $lastname,
        array  $roles,
    ): self {
        Assertion::notEmpty($firstname);
        Assertion::notEmpty($lastname);
        Assertion::email($email);
        return new self(
            uuid: $uuid,
            email: strtolower($email),
            firstname: $firstname,
            lastname: $lastname,
            roles: $roles,
        );
    }

    public function change(string $firstname, string $lastname, string $color)
    {
        Assertion::notEmpty($firstname);
        Assertion::notEmpty($lastname);
        Assertion::notEmpty($color);
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->color = $color;
    }

    public function addOrganisation(Organisation $organisation): void
    {
        if ($this->organisations->contains($organisation)) {
            return;
        }
        $this->organisations->add($organisation);
    }

    public function removeOrganisation(Organisation $organisation): void
    {
        $this->organisations->removeElement($organisation);
    }

    public function addOrganisationUser(OrganisationUser $organisationUser): void
    {
        if ($this->organisationUsers->contains($organisationUser)) {
            return;
        }
        $this->organisationUsers->add($organisationUser);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters and Setters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setTheme(InterfaceTheme $theme): void
    {
        $this->theme = $theme->value;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Extra getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getTheme(): InterfaceTheme
    {
        return InterfaceTheme::from($this->theme);
    }

    public function getThemeAsString(): string
    {
        return $this->theme;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @return Organisation[]
     */
    public function getOrganisations(): array
    {
        return $this->organisations->getValues();
    }

    /**
     * @return OrganisationUser[]
     */
    public function getOrganisationUsers(): array
    {
        return $this->organisationUsers->getValues();
    }

}
