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

class ResetPassword
{
    public string $uuid;
    public string $newPlainPassword;

    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(string $uuid, string $newPlainPassword)
    {
        $this->uuid = $uuid;
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
        return new self(
            $json->uuid,
            $json->newPlainPassword
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getNewPlainPassword(): string
    {
        return $this->newPlainPassword;
    }
}
