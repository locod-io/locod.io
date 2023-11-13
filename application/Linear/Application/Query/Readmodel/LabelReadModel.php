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

class LabelReadModel implements \JsonSerializable
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $color,
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->name = $this->getName();
        $json->color = $this->getColor();

        return $json;
    }

    public static function hydrateFromModel(array $model): self
    {
        return new self(
            trim($model['id']),
            trim($model['name']),
            trim($model['color']),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

}
