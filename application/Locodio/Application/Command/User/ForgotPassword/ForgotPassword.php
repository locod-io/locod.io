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

namespace App\Locodio\Application\Command\User\ForgotPassword;

class ForgotPassword
{
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self($json->email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
