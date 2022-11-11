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

namespace App\Locodio\Application\Command\User\CreateAccount;

class CreateAccount
{
    public function __construct(protected string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
