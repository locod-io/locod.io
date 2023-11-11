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

namespace App\Lodocio\Application\Query\Audit\Readmodel;

class AuditTrailCollection implements \JsonSerializable
{
    public function __construct(protected $collection = [])
    {
    }

    public function addItem(AuditTrailItem $item)
    {
        $this->collection[] = $item;
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->collection = $this->getCollection();
        return $json;
    }

    public function sortByDate(): void
    {
        usort($this->collection, function ($a, $b) {
            return $a->getCreatedAtNumber() - $b->getCreatedAtNumber();
        });
    }

    public function sortByDateDesc(): void
    {
        usort($this->collection, function ($a, $b) {
            return $b->getCreatedAtNumber() - $a->getCreatedAtNumber();
        });
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

}
