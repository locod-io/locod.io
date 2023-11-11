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

use App\Locodio\Application\Query\Linear\Readmodel\TeamReadModel;
use App\Locodio\Application\Query\Linear\Readmodel\TeamReadModelCollection;
use App\Lodocio\Domain\Model\Tracker\Tracker;

class TrackerReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                                  $id,
        protected string                               $uuid,
        protected int                                  $artefactId,
        protected int                                  $sequence,
        protected string                               $code,
        protected string                               $name,
        protected string                               $color,
        protected string                               $description,
        protected TrackerNodeStatusReadModelCollection $trackerNodeStatusReadModelCollection,
        protected TeamReadModelCollection              $relatedTeams,
        protected ?ProjectDocumentReadModel            $projectDocumentReadModel,
        protected null|array|\stdClass                 $structure = null,
        protected ?TrackerNodeReadModelCollection      $nodes = null,
        protected ?TrackerNodeGroupReadModelCollection $groups = null,
    )
    {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->artefactId = 'TRCKR-' . $this->getArtefactId();
        $json->sequence = $this->getSequence();
        $json->code = $this->getCode();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->description = $this->getDescription();
        $json->workflow = $this->getTrackerNodeStatusReadModelCollection()->getCollection();
        $json->teams = $this->getRelatedTeams()->getCollection();
        $json->relatedProjectDocument = $this->getProjectDocumentReadModel();

        if (!is_null($this->getStructure())) {
            $json->structure = $this->getStructure();
        }
        if (!is_null($this->getTrackerNodeReadModelCollection())) {
            $json->nodes = $this->getTrackerNodeReadModelCollection()->getCollection();
        }
        if (!is_null($this->getTrackerNodeGroupReadModelCollection())) {
            $json->groups = $this->getTrackerNodeGroupReadModelCollection()->getCollection();
        }

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Tracker $model, bool $full = false): self
    {
        $statusCollection = new TrackerNodeStatusReadModelCollection();
        foreach ($model->getTrackerNodeStatus() as $status) {
            $statusCollection->addItem(TrackerNodeStatusReadModel::hydrateFromModel($status));
        }
        $teams = new TeamReadModelCollection();
        foreach ($model->getRelatedTeams() as $team) {
            $teams->addItem(TeamReadModel::hydrateFromModel($team));
        }

        if (!$full) {
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getArtefactId(),
                $model->getSequence(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getDescription(),
                $statusCollection,
                $teams,
                ProjectDocumentReadModel::hydrateFromModel($model->getRelatedProjectDocument()),
            );
        } else {
            $nodesCollection = new TrackerNodeReadModelCollection();
            $groupCollection = new TrackerNodeGroupReadModelCollection();
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getArtefactId(),
                $model->getSequence(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getDescription(),
                $statusCollection,
                $teams,
                ProjectDocumentReadModel::hydrateFromModel($model->getRelatedProjectDocument()),
                $model->getStructure(),
                $nodesCollection,
                $groupCollection
            );
        }
    }

    public function setNodes(TrackerNodeReadModelCollection $nodes): void
    {
        $this->nodes = $nodes;
    }

    public function setGroups(TrackerNodeGroupReadModelCollection $groups): void
    {
        $this->groups = $groups;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTrackerNodeStatusReadModelCollection(): TrackerNodeStatusReadModelCollection
    {
        return $this->trackerNodeStatusReadModelCollection;
    }

    public function getStructure(): null|array|\stdClass
    {
        return $this->structure;
    }

    public function getTrackerNodeGroupReadModelCollection(): ?TrackerNodeGroupReadModelCollection
    {
        return $this->groups;
    }

    public function getTrackerNodeReadModelCollection(): ?TrackerNodeReadModelCollection
    {
        return $this->nodes;
    }

    public function getRelatedTeams(): TeamReadModelCollection
    {
        return $this->relatedTeams;
    }

    public function getProjectDocumentReadModel(): ?ProjectDocumentReadModel
    {
        return $this->projectDocumentReadModel;
    }

}
