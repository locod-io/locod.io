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
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRole;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable ()
 */
#[ORM\Table(name:"organisation_user_permissions")]
#[ORM\Entity(repositoryClass:\App\Locodio\Infrastructure\Database\OrganisationUserRepository::class)]
class OrganisationUser
{
    use TimestampableEntity;
    use BlameableEntity;
    use EntityId;
    use ChecksumEntity;

    #[ORM\Column]
    private array $roles;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\User\User", fetch:"EXTRA_LAZY", inversedBy:"organisationUsers")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Organisation", fetch:"EXTRA_LAZY", inversedBy:"organisationUsers")]
    #[ORM\JoinColumn(nullable: false)]
    private Organisation $organisation;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid $uuid,
        User $user,
        Organisation $organisation,
    ) {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->organisation = $organisation;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid $uuid,
        User $user,
        Organisation $organisation,
    ): self {
        return new self(
            $uuid,
            $user,
            $organisation,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Other Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRole::ROLE_USER->value;
        return array_unique($roles);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

}
