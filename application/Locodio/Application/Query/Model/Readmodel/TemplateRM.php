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

namespace App\Locodio\Application\Query\Model\Readmodel;

use App\Locodio\Domain\Model\Model\Template;

class TemplateRM implements \JsonSerializable
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int               $id,
        protected string            $uuid,
        protected string            $name,
        protected string            $type,
        protected string            $language,
        protected ?string           $template = null,
        protected ?MasterTemplateRM $masterTemplate = null
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Hydrate
    // ——————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Template $model, bool $full = false): self
    {
        if ($full) {
            $masterTemplate = null;
            if (!is_null($model->getMasterTemplate())) {
                $masterTemplate = MasterTemplateRM::hydrateFromModel($model->getMasterTemplate());
            }
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getTypeAsString(),
                $model->getLanguage(),
                $model->getTemplate(),
                $masterTemplate
            );
        } else {
            $rm = new self(
                $model->getId(),
                $model->getUuidAsString(),
                $model->getName(),
                $model->getTypeAsString(),
                $model->getLanguage()
            );
        }
        return $rm;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Serialize
    // ——————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->name = $this->getName();
        $json->type = $this->getType();
        $json->language = $this->getLanguage();
        if (!is_null($this->getTemplate())) {
            $json->template = $this->getTemplate();
        }
        $json->masterTemplate = $this->getMasterTemplate();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getMasterTemplate(): ?MasterTemplateRM
    {
        return $this->masterTemplate;
    }
}
