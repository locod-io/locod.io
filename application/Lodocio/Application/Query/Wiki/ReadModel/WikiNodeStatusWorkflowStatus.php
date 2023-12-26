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

class WikiNodeStatusWorkflowStatus implements \JsonSerializable
{
    public function __construct(
        protected string    $id,
        protected string    $label,
        protected string    $type,
        protected \stdClass $data,      // type and color
        protected \stdClass $position   // x and y
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->label = $this->getLabel();
        $json->type = $this->getType();
        $json->data = $this->getData();
        $json->position = $this->getPosition();
        return $json;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): \stdClass
    {
        return $this->data;
    }

    public function getPosition(): \stdClass
    {
        return $this->position;
    }
}
