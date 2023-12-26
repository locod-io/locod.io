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

namespace App\Locodio\Application\Command\User\CreateAccountFromGitHub;

class CreateAccountFromGitHub
{
    // ——————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————

    private function __construct(
        protected string $firstName,
        protected string $lastName,
        protected string $email,
        protected string $organisation,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————
    // Maker
    // ——————————————————————————————————————————————————————————————————————

    public static function make(
        string $name,
        string $email,
        string $company,
    ): self {
        $parts = explode(' ', $name, 2);
        $firstName = '*';
        $lastName = $name;
        if (count($parts) === 2) {
            $firstName = ucfirst($parts[0]);
            $lastName = ucfirst($parts[1]);
        } else {
            $emailWithoutDomain = explode('@', $email)[0];
            $partsFromEmail = explode('.', $emailWithoutDomain, 2);
            if (count($partsFromEmail) === 2) {
                $firstName = ucfirst($partsFromEmail[0]);
                $lastName = ucfirst($partsFromEmail[1]);
            }
        }
        return new self($firstName, $lastName, $email, $company);
    }

    // ——————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOrganisation(): string
    {
        return $this->organisation;
    }

}
