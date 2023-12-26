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

use App\Lodocio\Domain\Model\Wiki\WikiNodeGroup;

class WikiNodeGroupReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                                 $id,
        protected string                              $uuid,
        protected string                              $name,
        protected int                                 $level,
        protected string                              $number,
        protected bool                                $isOpen,
        protected WikiNodeReadModelCollection      $nodes,
        protected WikiNodeGroupReadModelCollection $groups,
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
        $json->name = $this->getName();
        $json->level = $this->getLevel();
        $json->number = $this->getNumber();
        $json->isOpen = $this->isOpen();
        $json->relatedProjectDocument = $this->getProjectDocumentReadModel();
        if (!is_null($this->getNodes())) {
            $json->nodes = $this->getNodes()->getCollection();
        }
        if (!is_null($this->getGroups())) {
            $json->groups = $this->getGroups()->getCollection();
        }
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public function addNode(WikiNodeReadModel $node): void
    {
        $this->nodes->addItem($node);
    }

    public function addGroup(WikiNodeGroupReadModel $group): void
    {
        $this->groups->addItem($group);
    }

    public static function hydrateFromModel(WikiNodeGroup $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getName(),
            $model->getLevel(),
            $model->getNumber(),
            $model->isOpen(),
            new WikiNodeReadModelCollection(),
            new WikiNodeGroupReadModelCollection(),
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public function getNodes(): ?WikiNodeReadModelCollection
    {
        return $this->nodes;
    }

    public function getGroups(): ?WikiNodeGroupReadModelCollection
    {
        return $this->groups;
    }

    public function getProjectDocumentReadModel(): ?ProjectDocumentReadModel
    {
        return $this->projectDocumentReadModel;
    }

}
