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

namespace App\Locodio\Application\Query\Organisation\Readmodel;

use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Application\Query\Model\Readmodel\CommandRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\EnumRM;
use App\Locodio\Application\Query\Model\Readmodel\EnumRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Application\Query\Model\Readmodel\QueryRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRMCollection;
use App\Locodio\Domain\Model\Organisation\Project;

class ProjectRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                      $id,
        protected string                   $uuid,
        protected string                   $code,
        protected string                   $name,
        protected string                   $color,
        protected string                   $domainLayer,
        protected string                   $applicationLayer,
        protected string                   $infrastructureLayer,
        protected ?OrganisationRM          $organisationRM = null,
        protected ?DomainModelRMCollection $domainModels = null,
        protected ?EnumRMCollection        $enums = null,
        protected ?QueryRMCollection       $queries = null,
        protected ?CommandRMCollection     $commands = null,
        protected ?TemplateRMCollection    $templates = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Project $model, bool $full = false): self
    {
        if ($full) {
            $templates = new TemplateRMCollection();
            foreach ($model->getTemplates() as $template) {
                $templates->addItem(TemplateRM::hydrateFromModel($template));
            }

            $commands = new CommandRMCollection();
            foreach ($model->getCommands() as $command) {
                $commands->addItem(CommandRM::hydrateFromModel($command));
            }

            $queries = new QueryRMCollection();
            foreach ($model->getQueries() as $query) {
                $queries->addItem(QueryRM::hydrateFromModel($query));
            }

            $enums = new EnumRMCollection();
            foreach ($model->getEnums() as $enum) {
                $enums->addItem(EnumRM::hydrateFromModel($enum));
            }

            $domainModels = new DomainModelRMCollection();
            foreach ($model->getDomainModels() as $domainModel) {
                $domainModels->addItem(DomainModelRM::hydrateFromModel($domainModel, true));
            }

            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getDomainLayer(),
                $model->getApplicationLayer(),
                $model->getInfrastructureLayer(),
                OrganisationRM::hydrateFromModel($model->getOrganisation()),
                $domainModels,
                $enums,
                $queries,
                $commands,
                $templates
            );
        } else {
            return new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getCode(),
                $model->getName(),
                $model->getColor(),
                $model->getDomainLayer(),
                $model->getApplicationLayer(),
                $model->getInfrastructureLayer(),
            );
        }
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->code = $this->getCode();
        $json->name = $this->getName();
        $json->color = $this->getColor();
        $json->domainLayer = $this->getDomainLayer();
        $json->applicationLayer = $this->getApplicationLayer();
        $json->infrastructureLayer = $this->getInfrastructureLayer();

        if (!is_null($this->getOrganisationRM())) {
            $json->organisation = $this->getOrganisationRM();
        }
        if (!is_null($this->getDomainModels())) {
            $json->domainModels = $this->getDomainModels()->getCollection();
        }
        if (!is_null($this->getEnums())) {
            $json->enums = $this->getEnums()->getCollection();
        }
        if (!is_null($this->getQueries())) {
            $json->queries = $this->getQueries()->getCollection();
        }
        if (!is_null($this->getCommands())) {
            $json->commands = $this->getCommands()->getCollection();
        }
        if (!is_null($this->getTemplates())) {
            $json->templates = $this->getTemplates()->getCollection();
        }
        return $json;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
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

    public function getOrganisationRM(): ?OrganisationRM
    {
        return $this->organisationRM;
    }

    public function getDomainModels(): ?DomainModelRMCollection
    {
        return $this->domainModels;
    }

    public function getEnums(): ?EnumRMCollection
    {
        return $this->enums;
    }

    public function getQueries(): ?QueryRMCollection
    {
        return $this->queries;
    }

    public function getCommands(): ?CommandRMCollection
    {
        return $this->commands;
    }

    public function getTemplates(): ?TemplateRMCollection
    {
        return $this->templates;
    }

    public function getDomainLayer(): string
    {
        return $this->domainLayer;
    }

    public function getApplicationLayer(): string
    {
        return $this->applicationLayer;
    }

    public function getInfrastructureLayer(): string
    {
        return $this->infrastructureLayer;
    }
}
