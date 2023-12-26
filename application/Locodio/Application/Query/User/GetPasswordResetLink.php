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

namespace App\Locodio\Application\Query\User;

use App\Locodio\Application\Query\User\Readmodel\PasswordResetLinkRM;
use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Symfony\Component\Uid\Uuid;

class GetPasswordResetLink
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository              $userRepo,
        protected PasswordResetLinkRepository $passwordResetLinkRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Read information
    // ———————————————————————————————————————————————————————————————

    public function ByHash(string $hash): PasswordResetLinkRM
    {
        return PasswordResetLinkRM::hydrateFromModel($this->passwordResetLinkRepo->getByCode($hash));
    }

    public function ByUuid(string $uuid): PasswordResetLinkRM
    {
        return PasswordResetLinkRM::hydrateFromModel($this->passwordResetLinkRepo->getByUuid(Uuid::fromString($uuid)));
    }

    public function isPasswordResetLinkIsValid(string $signature, int $verificationCode): bool
    {
        $link = $this->passwordResetLinkRepo->getByCode($signature);
        $signatureToCheck = hash('sha256', strtolower($link->getUser()->getEmail()) . $verificationCode . $_SERVER['APP_SECRET']);
        if ($signatureToCheck === $signature) {
            return true;
        }
        return false;
    }
}
