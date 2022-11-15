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

namespace App\Locodio\Application\Command\User\ResetPassword;

use ZxcvbnPhp\Zxcvbn;

class ResetPasswordHash
{
    private string $hash;
    private string $newPlainPassword;

    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(string $hash, string $newPlainPassword)
    {
        $this->hash = $hash;
        $this->newPlainPassword = $newPlainPassword;
    }

    public function isPasswordValid(): bool
    {
        $zxcvbn = new Zxcvbn();
        $passwordStrength = $zxcvbn->passwordStrength($this->newPlainPassword);
        if ($passwordStrength['score'] < 3) {
            return false;
        }
        return true;
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self($json->hash, $json->newPlainPassword);
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getNewPlainPassword(): string
    {
        return $this->newPlainPassword;
    }
}
