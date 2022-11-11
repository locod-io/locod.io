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

namespace App\Locodio\Application\Command\Model\AddEnumOption;

class AddEnumOption
{
    private function __construct(
        protected int    $enumId,
        protected string $code,
        protected string $value,
    ) {
    }

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->enumId,
            $json->code,
            $json->value,
        );
    }

    public function getEnumId(): int
    {
        return $this->enumId;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
