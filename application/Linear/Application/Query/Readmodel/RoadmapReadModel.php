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

namespace App\Linear\Application\Query\Readmodel;

class RoadmapReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected string                      $id,
        protected string                      $name,
        protected ?string                     $description = '',
        protected ?ProjectReadModelCollection $projectReadModelCollection = null,
    ) {
        if (is_null($this->projectReadModelCollection)) {
            $this->projectReadModelCollection = new ProjectReadModelCollection();
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->name = $this->getName();
        $json->description = $this->getDescription();
        $json->projects = $this->getProjectReadModelCollection()->getCollection();
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(array $model, bool $full = false): self
    {
        if ($full) {
            $projectReadModelCollection = new ProjectReadModelCollection();
            foreach ($model['projects']['nodes'] as $project) {
                $projectReadModelCollection->addItem(ProjectReadModel::hydrateFromModel($project));
            }
            $projectReadModelCollection->sortProjects();
            return new self(
                $model['id'],
                $model['name'],
                $model['description'],
                $projectReadModelCollection,
            );
        } else {
            return new self(
                $model['id'],
                $model['name'],
            );
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getProjectReadModelCollection(): ?ProjectReadModelCollection
    {
        return $this->projectReadModelCollection;
    }

}
