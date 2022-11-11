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
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\UserRegistrationLinkRepository::class)]
class UserRegistrationLink
{
    use TimestampableEntity;
    use BlameableEntity;
    use EntityId;
    use ChecksumEntity;

    // —————————————————————————————————————————————————————————————————————————————————
    // Properties
    // —————————————————————————————————————————————————————————————————————————————————

    #[ORM\Column(options: ["default" => 0])]
    private bool $isUsed;

    #[ORM\Column(length: 255)]
    private string $code;

    #[ORM\Column(length: 191)]
    private string $email;

    #[ORM\Column(length: 191)]
    private string $organisation;

    #[ORM\Column(length: 191)]
    private string $firstname;

    #[ORM\Column(length: 191)]
    private string $lastname;

    #[ORM\Column(length: 191)]
    private string $password;

    private function __construct(
        Uuid   $uuid,
        string $email,
        string $organisation,
        string $firstname,
        string $lastname,
        string $password
    ) {
        $this->uuid = $uuid;
        $this->isUsed = false;
        $this->email = $email;
        $this->organisation = $organisation;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->code = hash('sha1', $uuid->toRfc4122());
    }

    public static function make(
        Uuid   $uuid,
        string $email,
        string $organisation,
        string $firstname,
        string $lastname,
        string $password
    ): self {
        return new self(
            $uuid,
            $email,
            $organisation,
            $firstname,
            $lastname,
            $password
        );
    }

    public function useLink(): void
    {
        $this->isUsed = true;
        $this->password = '';
        $this->organisation = '**';
        $this->firstname = '**';
        $this->lastname = '**';
        $this->email = '**';
    }

    // —————————————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————————————

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOrganisation(): string
    {
        return $this->organisation;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
