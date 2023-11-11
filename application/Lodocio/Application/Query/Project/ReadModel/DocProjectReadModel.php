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

namespace App\Lodocio\Application\Query\Project\ReadModel;

use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModel;
use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerReadModelCollection;
use App\Lodocio\Domain\Model\Project\DocProject;

class DocProjectReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                        $id,
        protected string                     $uuid,
        protected string                     $name,
        protected string                     $code,
        protected string                     $color,
        protected ProjectRM                  $project,
        protected TrackerReadModelCollection $trackers,
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
        $json->code = $this->getCode();
        $json->color = $this->getColor();
        $json->project = $this->getProject();
        $json->trackers = $this->getTrackers()->getCollection();

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(DocProject $model): self
    {
        $trackers = new TrackerReadModelCollection();
        foreach ($model->getTrackers() as $tracker) {
            $trackers->addItem(TrackerReadModel::hydrateFromModel($tracker));
        }
        $relatedProject = ProjectRM::hydrateFromModel($model->getProject(), false, true);

        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getName(),
            $model->getCode(),
            $model->getColor(),
            $relatedProject,
            $trackers
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getTrackers(): TrackerReadModelCollection
    {
        return $this->trackers;
    }

    public function getProject(): ProjectRM
    {
        return $this->project;
    }

}
