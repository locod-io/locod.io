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

use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\MasterTemplateForkRepository::class)]
class MasterTemplateFork
{
    use EntityId;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column]
    private \DateTimeImmutable $forkedAt;

    #[ORM\Column]
    private string $forkedBy;

    #[ORM\Column(type: 'uuid')]
    private Uuid $masterTemplateSource;

    #[ORM\Column(type: 'uuid')]
    private Uuid $masterTemplateTarget;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid   $uuid,
        Uuid   $masterTemplateSource,
        Uuid   $masterTemplateTarget,
        string $forkedBy
    ) {
        $this->uuid = $uuid;
        $this->forkedAt = new \DateTimeImmutable();
        $this->masterTemplateSource = $masterTemplateSource;
        $this->masterTemplateTarget = $masterTemplateTarget;
        $this->forkedBy = $forkedBy;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker and changers
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid   $uuid,
        Uuid   $masterTemplateSource,
        Uuid   $masterTemplateTarget,
        string $forkedBy
    ): self {
        return new self(
            $uuid,
            $masterTemplateSource,
            $masterTemplateTarget,
            $forkedBy
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getForkedAt(): \DateTimeImmutable
    {
        return $this->forkedAt;
    }

    public function getForkedBy(): string
    {
        return $this->forkedBy;
    }

    public function getMasterTemplateSource(): Uuid
    {
        return $this->masterTemplateSource;
    }

    public function getMasterTemplateTarget(): Uuid
    {
        return $this->masterTemplateTarget;
    }
}
