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

namespace App\Locodio\Application\Command\User\ValidateInvitation;

class ValidationInvitation
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected string $signature,
        protected int    $verificationCode,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydrate from JSON
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            signature: $json->signature,
            verificationCode: $json->verificationCode,
        );
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

}
