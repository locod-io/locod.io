<?php

namespace App\Locodio\Application\Command\Model\ChangeDocumentorRelatedIssues;

class ChangeDocumentorRelatedIssues
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int   $id,
        protected array $relatedIssues,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            intval($json->id),
            $json->relatedIssues,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getRelatedIssues(): array
    {
        return $this->relatedIssues;
    }

}
