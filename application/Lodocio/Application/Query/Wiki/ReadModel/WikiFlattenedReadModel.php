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

use App\Lodocio\Domain\Model\Wiki\Wiki;

class WikiFlattenedReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $uuid,
        protected string $name,
        protected string $code,
        protected array  $elements,
    ) {
    }

    // —————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->name = $this->getName();
        $json->code = $this->getCode();
        $json->elements = $this->getElements();
        return $json;
    }

    // —————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(Wiki $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getName(),
            $model->getCode(),
            [],
        );
    }

    // —————————————————————————————————————————————————————————————————
    // Setters
    // —————————————————————————————————————————————————————————————————

    public function addElement($element): void
    {
        $this->elements[] = $element;
    }

    // —————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————

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

    public function getElements(): array
    {
        return $this->elements;
    }
}
