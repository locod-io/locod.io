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

class UserInvitationLinkRMCollection implements \JsonSerializable
{
    public function __construct(
        protected array $collection = []
    ) {
    }

    public function addItem(UserInvitationLinkRM $item): void
    {
        $this->collection[] = $item;
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->collection = $this->getCollection();
        return $json;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

}
