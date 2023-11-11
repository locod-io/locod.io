<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Domain\Model\Tracker;

use App\Lodocio\Domain\Model\Common\ChecksumEntity;
use App\Lodocio\Domain\Model\Common\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Lodocio\Infrastructure\Database\Tracker\TrackerRelatedProjectDocumentRepository::class)]
class TrackerRelatedProjectDocument
{
    use EntityId;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 191)]
    private string $relatedProjectId;

    #[ORM\Column(length: 191)]
    private string $relatedDocumentId;

    #[ORM\Column(length: 191)]
    private string $title;

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        Uuid   $uuid,
        string $relatedProjectId,
        string $relatedDocumentId,
        string $title,
    )
    {
        $this->uuid = $uuid;
        $this->relatedProjectId = $relatedProjectId;
        $this->relatedDocumentId = $relatedDocumentId;
        $this->title = $title;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Maker
    // —————————————————————————————————————————————————————————————————————————

    public static function make(
        Uuid   $uuid,
        string $relatedProjectId,
        string $relatedDocumentId,
        string $title,
    ): self
    {
        return new self(
            $uuid,
            $relatedProjectId,
            $relatedDocumentId,
            $title
        );
    }

    public function change(
        string $relatedProjectId,
        string $relatedDocumentId,
        string $title,
    ): void
    {
        $this->relatedProjectId = $relatedProjectId;
        $this->relatedDocumentId = $relatedDocumentId;
        $this->title = $title;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getRelatedProjectId(): string
    {
        return $this->relatedProjectId;
    }

    public function getRelatedDocumentId(): string
    {
        return $this->relatedDocumentId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

}
