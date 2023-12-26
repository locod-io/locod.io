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

class WikiNodeStatusWorkflowRelation implements \JsonSerializable
{
    public function __construct(
        protected string    $id,
        protected string    $source,
        protected string    $target,
        protected bool      $animated,  // false
        protected \stdClass $style,     // stroke, strokeWidth
        protected \stdClass $data       // color
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->source = $this->getSource();
        $json->target = $this->getTarget();
        $json->animated = $this->isAnimated();
        $json->style = $this->getStyle();
        $json->data = $this->getData();
        return $json;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function isAnimated(): bool
    {
        return $this->animated;
    }

    public function getStyle(): \stdClass
    {
        return $this->style;
    }

    public function getData(): \stdClass
    {
        return $this->data;
    }
}
