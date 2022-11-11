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

namespace App\Locodio\Application\Command\User\ChangeProfile;

class ChangeProfile
{
    public function __construct(
        protected int    $userId,
        protected string $firstname,
        protected string $lastname,
        protected string $color,
    ) {
    }

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            $json->userId,
            $json->firstname,
            $json->lastname,
            $json->color
        );
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
