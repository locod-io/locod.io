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

use App\Locodio\Domain\Model\Common\ArtefactEntity;
use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\EnumOptionRepository::class)]
class EnumOption
{
    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;
    use ArtefactEntity;

    #[ORM\Column(length: 191)]
    private string $code;

    #[ORM\Column(length: 191)]
    private string $value;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\Enum", fetch: "EXTRA_LAZY", inversedBy: "options")]
    #[ORM\JoinColumn(nullable: false)]
    private Enum $enum;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor & assertion
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(
        Enum   $enum,
        Uuid   $uuid,
        string $code,
        string $value
    ) {
        $this->enum = $enum;
        $this->uuid = $uuid;
        $this->code = $code;
        $this->value = $value;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // ———————————————————————————————————————————————————————————————————————————————————————

    public static function make(
        Enum   $enum,
        Uuid   $uuid,
        string $code,
        string $value
    ): self {
        return new self(
            $enum,
            $uuid,
            $code,
            $value
        );
    }

    public function change(
        string $code,
        string $value
    ): void {
        $this->code = $code;
        $this->value = $value;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getEnum(): Enum
    {
        return $this->enum;
    }
}
