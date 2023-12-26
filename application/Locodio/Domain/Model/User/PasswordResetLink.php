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
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\PasswordResetLinkRepository::class)]
class PasswordResetLink
{
    use TimestampableEntity;
    use BlameableEntity;
    use EntityId;
    use ChecksumEntity;

    // —————————————————————————————————————————————————————————————————————————————————
    // Properties
    // —————————————————————————————————————————————————————————————————————————————————

    #[ORM\Column(options: ["default" => 1])]
    private bool $isActive;

    #[ORM\Column(options: ["default" => 0])]
    private bool $isUsed;

    #[ORM\Column]
    private \DateTime $expiresAt;

    #[ORM\Column(length: 255)]
    private string $code;

    // —————————————————————————————————————————————————————————————————————————————————
    // Relations
    // —————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\User\User", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    // —————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————————————

    private function __construct(Uuid $uuid, User $user, string $code)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->isActive = true;
        $this->isUsed = false;
        $now = new \DateTime();
        $this->expiresAt = $now->add(new \DateInterval('P3D'));
        $this->code = $code;
    }

    public static function make(Uuid $uuid, User $user, string $code): self
    {
        return new self($uuid, $user, $code);
    }

    public function useLink(string $signature): void
    {
        if ($this->code === $signature) {
            $this->isActive = false;
            $this->isUsed = true;
        }
    }

    public function inValidate(): void
    {
        $this->isActive = false;
    }

    // —————————————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————————————

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
