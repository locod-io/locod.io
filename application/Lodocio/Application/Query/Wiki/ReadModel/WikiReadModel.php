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

namespace App\Lodocio\Application\Query\Wiki\ReadModel;

use App\Linear\Application\Query\Readmodel\TeamReadModel;
use App\Linear\Application\Query\Readmodel\TeamReadModelCollection;
use App\Lodocio\Domain\Model\Wiki\Wiki;

class WikiReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                               $id,
        protected string                            $uuid,
        protected int                               $artefactId,
        protected int                               $sequence,
        protected string                            $code,
        protected string                            $name,
        protected string                            $color,
        protected string $icon,
        protected string                            $description,
        protected string                            $slug,
        protected bool                              $isPublic,
        protected bool                              $showOnlyFinalNodes,
        protected WikiNodeStatusReadModelCollection $wikiNodeStatusReadModelCollection,
        protected TeamReadModelCollection           $relatedTeams,
        protected ?ProjectDocumentReadModel         $projectDocumentReadModel,
        protected null|array|\stdClass              $structure = null,
        protected ?WikiNodeReadModelCollection      $nodes = null,
        protected ?WikiNodeGroupReadModelCollection $groups = null,
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
        $json->artefactId = 'WK-' . $this->getArtefactId();
        $json->sequence = $this->getSequence();
        $json->code = $this->getCode();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->icon = $this->getIcon();
        $json->description = $this->getDescription();
        $json->slug = $this->getSlug();
        $json->isPublic = $this->isPublic();
        $json->showOnlyFinalNodes = $this->showOnlyFinalNodes();

        $json->workflow = $this->getWikiNodeStatusReadModelCollection()->getCollection();
        $json->teams = $this->getRelatedTeams()->getCollection();
        $json->relatedProjectDocument = $this->getProjectDocumentReadModel();

        if (!is_null($this->getStructure())) {
            $json->structure = $this->getStructure();
        }
        if (!is_null($this->getWikiNodeReadModelCollection())) {
            $json->nodes = $this->getWikiNodeReadModelCollection()->getCollection();
        }
        if (!is_null($this->getWikiNodeGroupReadModelCollection())) {
            $json->groups = $this->getWikiNodeGroupReadModelCollection()->getCollection();
        }

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Wiki $model, bool $full = false): self
    {
        $statusCollection = new WikiNodeStatusReadModelCollection();
        foreach ($model->getWikiNodeStatus() as $status) {
            $statusCollection->addItem(WikiNodeStatusReadModel::hydrateFromModel($status));
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
                $model->getIcon(),
                $model->getDescription(),
                $model->getSlug(),
                $model->isPublic(),
                $model->showOnlyFinalNodes(),
                $statusCollection,
                $teams,
                ProjectDocumentReadModel::hydrateFromModel($model->getRelatedProjectDocument()),
            );
        } else {
            $nodesCollection = new WikiNodeReadModelCollection();
            $groupCollection = new WikiNodeGroupReadModelCollection();
            return new self(
                $model->getId(),
                $model->getUuid()->toRfc4122(),
                $model->getArtefactId(),
                $model->getSequence(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getIcon(),
                $model->getDescription(),
                $model->getSlug(),
                $model->isPublic(),
                $model->showOnlyFinalNodes(),
                $statusCollection,
                $teams,
                ProjectDocumentReadModel::hydrateFromModel($model->getRelatedProjectDocument()),
                $model->getStructure(),
                $nodesCollection,
                $groupCollection
            );
        }
    }

    public function setNodes(WikiNodeReadModelCollection $nodes): void
    {
        $this->nodes = $nodes;
    }

    public function setGroups(WikiNodeGroupReadModelCollection $groups): void
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

    public function getWikiNodeStatusReadModelCollection(): WikiNodeStatusReadModelCollection
    {
        return $this->wikiNodeStatusReadModelCollection;
    }

    public function getStructure(): null|array|\stdClass
    {
        return $this->structure;
    }

    public function getWikiNodeGroupReadModelCollection(): ?WikiNodeGroupReadModelCollection
    {
        return $this->groups;
    }

    public function getWikiNodeReadModelCollection(): ?WikiNodeReadModelCollection
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function showOnlyFinalNodes(): bool
    {
        return $this->showOnlyFinalNodes;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

}
