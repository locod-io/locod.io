<?php

namespace App\Lodocio\Application\Command\Tracker\LinkProjectDocument;

use App\Lodocio\Application\Command\Tracker\ProjectDocumentType;

class LinkProjectDocument
{

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectDocumentType $type,
        protected int                 $subjectId,
        protected string              $relatedProjectId,
        protected string              $relatedDocumentId,
    )
    {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            ProjectDocumentType::tryFrom($json->type),
            (int)$json->subjectId,
            $json->relatedProjectId,
            $json->relatedDocumentId
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getType(): ProjectDocumentType
    {
        return $this->type;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getRelatedProjectId(): string
    {
        return $this->relatedProjectId;
    }

    public function getRelatedDocumentId(): string
    {
        return $this->relatedDocumentId;
    }

}