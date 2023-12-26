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

use App\Locodio\Domain\Model\User\PasswordResetLinkRepository;
use App\Locodio\Domain\Model\User\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class ResetPasswordHandler
{
    public const LINK_NOT_ACTIVE = 'link_not_active';
    public const PASSWORD_IS_RESET = 'password_is_reset';

    // ———————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————

    protected UserRepository $userRepo;
    protected PasswordResetLinkRepository $passwordResetLinkRepo;
    protected UserPasswordHasherInterface $passwordEncoder;

    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository              $userRepository,
        PasswordResetLinkRepository $passwordResetLinkRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepo = $userRepository;
        $this->passwordResetLinkRepo = $passwordResetLinkRepository;
    }

    // ———————————————————————————————————————————————————————————————
    // Commands
    // ———————————————————————————————————————————————————————————————

    public function UserResetPassword(ResetPassword $command): ?int
    {
        if (!$command->isPasswordValid()) {
            throw new \Exception('Password is not valid.');
        }
        $user = $this->userRepo->getByUuid(Uuid::fromString($command->getUuid()));
        $user->setPassword($this->passwordEncoder->hashPassword($user, $command->getNewPlainPassword()));

        return $this->userRepo->save($user);
    }

    public function AddUserPassword(ResetPassword $command): ?int
    {
        if (!$command->isPasswordValid()) {
            throw new \Exception('Password is not valid.');
        }
        $user = $this->userRepo->getByUuid(Uuid::fromString($command->getUuid()));
        $user->setPassword($this->passwordEncoder->hashPassword($user, $command->getNewPlainPassword()));

        return $this->userRepo->save($user);
    }

    public function UserResetPasswordHash(ResetPasswordHash $command): string
    {
        $resetLink = $this->passwordResetLinkRepo->getByCode($command->getSignature());

        // -- check of link is active
        if (!$resetLink->isActive() && $resetLink->isUsed()) {
            return self::LINK_NOT_ACTIVE;
        }
        // -- check of password is strong enough
        if (!$command->isPasswordValid()) {
            throw new \Exception('Password is not valid.');
        }
        // -- check of signature and verification code are matching
        $signatureToCheck = hash('sha256', strtolower($resetLink->getUser()->getEmail()) . $command->getVerificationCode() . $_SERVER['APP_SECRET']);
        if ($signatureToCheck !== $command->getSignature()) {
            throw new \Exception('Verification code is not valid.');
        }

        $user = $this->userRepo->getById($resetLink->getUser()->getId());
        $user->setPassword($this->passwordEncoder->hashPassword($user, $command->getNewPlainPassword()));
        $userId = $this->userRepo->save($user);
        $resetLink->useLink($command->getSignature());
        $resetLinkId = $this->passwordResetLinkRepo->save($resetLink);

        // -- invalidate all other reset links
        $resetLinks = $this->passwordResetLinkRepo->getByUser($user->getId());
        foreach ($resetLinks as $otherResetLink) {
            if ($otherResetLink->getId() != $resetLinkId) {
                $otherResetLink->inValidate();
                $otherResetLinkId = $this->passwordResetLinkRepo->save($otherResetLink);
            }
        }

        return self::PASSWORD_IS_RESET;
    }
}
