<?php

namespace App\Lodocio\Application\Command\Wiki\UploadFigmaExportImage;

class UploadFigmaExportImage
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected int    $nodeId,
        protected string $figmaDocumentKey,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            (int) $json->nodeId,
            (string) $json->figmaDocumentKey,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getNodeId(): int
    {
        return $this->nodeId;
    }

    public function getFigmaDocumentKey(): string
    {
        return $this->figmaDocumentKey;
    }

}
