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

namespace App\Locodio\Application\Command\User\Register;

use Assert\Assertion;
use ZxcvbnPhp\Zxcvbn;

class Register
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected string $organisation,
        protected string $firstname,
        protected string $lastname,
        protected string $email,
        protected string $password1,
        protected string $password2
    ) {
        Assertion::notEmpty($this->organisation);
        Assertion::notEmpty($this->firstname);
        Assertion::notEmpty($this->lastname);
        Assertion::email($this->email);
        Assertion::notEmpty($this->password1);
        Assertion::notEmpty($this->password2);
        Assertion::eq($this->password1, $this->password2);
    }

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

    public function getOrganisation(): string
    {
        return $this->organisation;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
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
