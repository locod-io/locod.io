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

use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Domain\Model\User\User;

class AnonymousUserRM implements \JsonSerializable
{
    // ———————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        protected string $initials,
        protected string $color
    ) {
    }

    // ———————————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(User $model): self
    {
        return new self(
            strtoupper(substr($model->getFirstname(), 0, 1) . substr($model->getLastname(), 0, 1)),
            $model->getColor()
        );
    }

    // ———————————————————————————————————————————————————————————————————
    // What to render as JSON
    // ———————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->color = $this->getColor();
        $json->initials = $this->getInitials();

        return $json;
    }

    // ———————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————

    public function getInitials(): string
    {
        return $this->initials;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
