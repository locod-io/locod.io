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
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\UserInvitationLinkRepository::class)]
class UserInvitationLink
{
    use TimestampableEntity;
    use BlameableEntity;
    use EntityId;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $email;

    #[ORM\Column(length: 191)]
    private string $code;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isUsed;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Organisation", fetch: "EXTRA_LAZY", inversedBy: "userInvitationLinks")]
    #[ORM\JoinColumn(nullable: false)]
    private Organisation $organisation;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid         $uuid,
        string       $email,
        string       $code,
        Organisation $organisation
    ) {
        $this->uuid = $uuid;
        $this->email = $email;
        $this->code = $code;
        $this->organisation = $organisation;
        $this->isUsed = false;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid         $uuid,
        string       $email,
        Organisation $organisation,
        string       $code
    ): self {
        return new self(
            uuid: $uuid,
            email: $email,
            code: $code,
            organisation: $organisation,
        );
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function invalidate(): void
    {
        $this->isUsed = true;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

}
