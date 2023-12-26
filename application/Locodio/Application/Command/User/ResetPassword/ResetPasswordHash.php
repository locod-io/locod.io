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
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected string $signature,
        protected int    $verificationCode,
        protected string $newPlainPassword
    ) {
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
        return new self($json->signature, $json->verificationCode, $json->newPlainPassword);
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getVerificationCode(): int
    {
        return $this->verificationCode;
    }

    public function getNewPlainPassword(): string
    {
        return $this->newPlainPassword;
    }
}
