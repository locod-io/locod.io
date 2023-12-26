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

namespace App\Locodio\Application\Command\User\CreateAccountFromInvitation;

use ZxcvbnPhp\Zxcvbn;

class CreateAccountFromInvitation
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected string $signature,
        protected int    $code,
        protected string $firstName,
        protected string $lastName,
        protected string $password1,
        protected string $password2,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Validation
    // ———————————————————————————————————————————————————————————————

    public function isPasswordValid(): bool
    {
        if ($this->password1 !== $this->password2) {
            return false;
        }
        $zxcvbn = new Zxcvbn();
        $passwordStrength = $zxcvbn->passwordStrength($this->password1);
        if ($passwordStrength['score'] < 3) {
            return false;
        }
        return true;
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword1(): string
    {
        return $this->password1;
    }

    public function getPassword2(): string
    {
        return $this->password2;
    }

}
