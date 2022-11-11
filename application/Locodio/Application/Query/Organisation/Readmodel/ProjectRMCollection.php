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

class ProjectRMCollection implements \JsonSerializable
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        private array $collection = []
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // What to render as json
    // ———————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->collection = $this->getCollection();
        return $json;
    }

    public function addItem(ProjectRM $rm)
    {
        $this->collection[] = $rm;
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————
    /**
     * @return ProjectRM[]
     */
    public function getCollection(): array
    {
        return $this->collection;
    }
}
