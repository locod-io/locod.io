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

namespace App\Locodio\Application\Query\User\Readmodel;

class UserRMCollection implements \JsonSerializable
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

    public function addItem(UserRM $rm)
    {
        $this->collection[] = $rm;
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————
    /**
     * @return UserRM[]
     */
    public function getCollection(): array
    {
        return $this->collection;
    }
}
