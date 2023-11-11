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

namespace App\Locodio\Application\Query\Linear\Readmodel;

class ProjectReadModel implements \JsonSerializable
{
    public function __construct(
        protected string                   $id,
        protected string                   $name,
        protected string                   $url,
        protected string                   $color,
        protected ?TeamReadModelCollection $teams = null,
        protected string                   $description = '',
        protected string                   $startDate = '',
        protected string                   $targetDate = '',
        protected string                   $state = '',
        protected string                   $lead = '',
        protected float                    $sortOrder = 0,
    )
    {
        if (is_null($this->teams)) {
            $this->teams = new TeamReadModelCollection();
        }
    }

    public static function hydrateFromModel(array $model): self
    {
        $teamCollection = new TeamReadModelCollection();
        if (isset($model['teams']['nodes'])) {
            foreach ($model['teams']['nodes'] as $team) {
                $teamCollection->addItem(TeamReadModel::hydrateFromModel($team));
            }
        }
        $description = '';
        $startDate = '';
        $targetDate = '';
        $state = '';
        $lead = '';
        $sortOrder = 0;

        if (isset($model['description']) && !is_null($model['description'])) {
            $description = $model['description'];
        }
        if (isset($model['startDate']) && !is_null($model['startDate'])) {
            $startDate = $model['startDate'];
        }
        if (isset($model['targetDate']) && !is_null($model['targetDate'])) {
            $targetDate = $model['targetDate'];
        }
        if (isset($model['state']) && !is_null($model['state'])) {
            $state = $model['state'];
        }
        if (isset($model['lead']) && !is_null($model['lead'])) {
            $lead = $model['lead']['name'];
        }
        if (isset($model['sortOrder']) && !is_null($model['sortOrder'])) {
            $sortOrder = (float)$model['sortOrder'];
        }

        return new self(
            trim($model['id']),
            trim($model['name']),
            trim($model['url']),
            trim($model['color']),
            $teamCollection,
            $description,
            $startDate,
            $targetDate,
            $state,
            $lead,
            $sortOrder,
        );
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->name = $this->getName();
        $json->url = $this->getUrl();
        $json->color = $this->getColor();
        $json->description = $this->getDescription();
        $json->startDate = $this->getStartDate();
        $json->targetDate = $this->getTargetDate();
        $json->state = $this->getState();
        $json->lead = $this->getLead();
        $json->teams = $this->getTeams()->getCollection();

        return $json;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getTargetDate(): string
    {
        return $this->targetDate;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getLead(): string
    {
        return $this->lead;
    }

    public function getSortOrder(): float
    {
        return $this->sortOrder;
    }

    public function getTeams(): ?TeamReadModelCollection
    {
        return $this->teams;
    }

}
