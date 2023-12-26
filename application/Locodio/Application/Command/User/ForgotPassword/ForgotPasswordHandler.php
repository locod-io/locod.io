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

use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Doctrine\ORM\EntityNotFoundException;

class ForgotPasswordHandler
{
    private UserRepository $userRepo;
    private PasswordResetLinkRepository $passwordResetLinkRepo;

    // —————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————

    public function __construct(
        UserRepository              $userRepository,
        PasswordResetLinkRepository $passwordResetLinkRepository
    ) {
        $this->userRepo = $userRepository;
        $this->passwordResetLinkRepo = $passwordResetLinkRepository;
    }

    // —————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————

    public function ForgotPassword(ForgotPassword $command): \stdClass
    {
        $result = new \stdClass();
        $result->message = '';
        try {
            $user = $this->userRepo->getByEmail($command->getEmail());
        } catch (EntityNotFoundException $exception) {
            $result->message = 'no_user_found';
            return $result;
        }

        // -- invalidate all other user links
        $resetLinks = $this->passwordResetLinkRepo->getByUser($user->getId());
        foreach ($resetLinks as $resetLink) {
            $resetLink->inValidate();
            $resetLinkId = $this->passwordResetLinkRepo->save($resetLink);
        }

        // -- make a new reset link
        $verificationCode = random_int(100000, 999999);
        $signature = hash('sha256', strtolower($command->getEmail()) . $verificationCode . $_SERVER['APP_SECRET']);

        $newResetLink = PasswordResetLink::make($this->passwordResetLinkRepo->nextIdentity(), $user, $signature);
        $resetLinkId = $this->passwordResetLinkRepo->save($newResetLink);

        // -- send reset link to user
        $result->uuid = $newResetLink->getUuid()->toRfc4122();
        $result->signature = $signature;
        $result->verificationCode = $verificationCode;
        $result->message = 'reset_link_sent';
        return $result;
    }
}
