<?php

declare(strict_types=1);

namespace App\Lodocio\Application\Query\Tracker\ReadModel;

use App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocument;

class ProjectDocumentReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $uuid,
        protected string $relatedProjectId,
        protected string $relatedDocumentId,
        protected string $title,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->relatedProjectId = $this->getRelatedProjectId();
        $json->relatedDocumentId = $this->getRelatedDocumentId();
        $json->title = $this->getTitle();

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(?TrackerRelatedProjectDocument $model): ?self
    {
        if (is_null($model)) {
            return null;
        } else {
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getRelatedProjectId(),
                $model->getRelatedDocumentId(),
                $model->getTitle(),
            );
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

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
