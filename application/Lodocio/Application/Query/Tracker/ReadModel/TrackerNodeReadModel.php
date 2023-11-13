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

namespace App\Lodocio\Application\Query\Tracker\ReadModel;

use App\Lodocio\Domain\Model\Tracker\TrackerNode;

class TrackerNodeReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                                $id,
        protected string                             $uuid,
        protected int                                $artefactId,
        protected int                                $sequence,
        protected string                             $number,
        protected int                                $level,
        protected string                             $name,
        protected bool                               $isOpen,
        protected string                             $description,
        protected array                              $relatedIssues,
        protected ?\DateTimeImmutable                $finalAt,
        protected string                             $finalBy,
        protected TrackerNodeStatusReadModel         $trackerNodeStatusReadModel,
        protected TrackerNodeFileReadModelCollection $fileReadModelCollection,
        protected ?ProjectDocumentReadModel           $projectDocumentReadModel,
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
        $json->artefactId = $this->getArtefactId();
        $json->sequence = $this->getSequence();
        $json->number = $this->getNumber();
        $json->level = $this->getLevel();
        $json->isOpen = $this->isOpen();
        $json->name = $this->getName();
        $json->description = $this->getDescription();
        $json->relatedIssues = $this->getRelatedIssues();
        $json->finalBy = $this->getFinalBy();
        if (is_null($this->finalAt)) {
            $json->finalAt = '';
        } else {
            $json->finalAt = $this->getFinalAt()->format(\DateTimeInterface::ATOM);
        }
        $json->status = $this->trackerNodeStatusReadModel;
        $json->files = $this->getFileReadModelCollection()->getCollection();
        $json->relatedProjectDocument = $this->getProjectDocumentReadModel();
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(TrackerNode $model): self
    {
        $fileCollection = new TrackerNodeFileReadModelCollection();
        foreach ($model->getTrackerNodeFiles() as $file) {
            $fileCollection->addItem(TrackerNodeFileReadModel::hydrateFromModel($file));
        }
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getArtefactId(),
            $model->getSequence(),
            $model->getNumber(),
            $model->getLevel(),
            $model->getName(),
            $model->isOpen(),
            $model->getDescription(),
            $model->getRelatedIssues(),
            $model->getFinalAt(),
            $model->getFinalBy(),
            TrackerNodeStatusReadModel::hydrateFromModel($model->getTrackerNodeStatus()),
            $fileCollection,
            ProjectDocumentReadModel::hydrateFromModel($model->getRelatedProjectDocument())
        );
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

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRelatedIssues(): array
    {
        return $this->relatedIssues;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getFinalAt(): ?\DateTimeImmutable
    {
        return $this->finalAt;
    }

    public function getFinalBy(): string
    {
        return $this->finalBy;
    }

    public function getTrackerNodeStatusReadModel(): TrackerNodeStatusReadModel
    {
        return $this->trackerNodeStatusReadModel;
    }

    public function getFileReadModelCollection(): TrackerNodeFileReadModelCollection
    {
        return $this->fileReadModelCollection;
    }

    public function getProjectDocumentReadModel(): ?ProjectDocumentReadModel
    {
        return $this->projectDocumentReadModel;
    }

}