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
use Symfony\Component\Security\Core\Security;

class ForgotPasswordHandler
{
    private Security $security;
    private UserRepository $userRepo;
    private PasswordResetLinkRepository $passwordResetLinkRepo;

    // —————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————

    public function __construct(
        Security $security,
        UserRepository $userRepository,
        PasswordResetLinkRepository $passwordResetLinkRepository
    ) {
        $this->security = $security;
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
        // ------------------------------ invalidate all other user links
        $resetLinks = $this->passwordResetLinkRepo->getByUser($user->getId());
        foreach ($resetLinks as $resetLink) {
            $resetLink->inValidate();
            $resetLinkId = $this->passwordResetLinkRepo->save($resetLink);
        }
        // ---------------------------------------- make a new reset link
        $newResetLink = PasswordResetLink::make($this->passwordResetLinkRepo->nextIdentity(), $user);
        $resetLinkId = $this->passwordResetLinkRepo->save($newResetLink);
        // -------------------------------------- send reset link to user
        $result->uuid = $newResetLink->getUuid()->toRfc4122();
        $result->message = 'reset_link_sent';
        return $result;
    }
}
