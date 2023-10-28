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

namespace App\Locodio\Application\Query\Model\Readmodel;

class GeneratedCodeRM implements \JsonSerializable
{
    public function __construct(
        protected string $code,
        protected bool   $isGenerated = true,
        protected string $errorMessage = '',
    ) {
    }

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->code = $this->getCode();
        $json->isGenerated = $this->isGenerated();
        $json->errorMessage = $this->getErrorMessage();
        return $json;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

}
