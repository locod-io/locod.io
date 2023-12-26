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

use App\Locodio\Domain\Model\User\UserInvitationLinkRepository;

class ValidationInvitationHandler
{
    public function __construct(protected UserInvitationLinkRepository $invitationLinkRepository)
    {
    }

    public function validate(ValidationInvitation $command): bool
    {
        $invitationLink = $this->invitationLinkRepository->getByCode($command->getSignature());
        if($invitationLink->isUsed()) {
            return false;
        }
        $signatureToCheck = hash('sha256', $invitationLink->getEmail() . $command->getVerificationCode() . $_SERVER['APP_SECRET']);
        if ($signatureToCheck === $command->getSignature()) {
            return true;
        } else {
            return false;
        }
    }
}
