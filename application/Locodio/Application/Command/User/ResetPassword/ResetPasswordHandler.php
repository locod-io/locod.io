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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class ResetPasswordHandler
{
    public const LINK_NOT_ACTIVE = 'link_not_active';
    public const PASSWORD_IS_RESET = 'password_is_reset';

    // ———————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————

    protected Security $security;
    protected UserRepository $userRepo;
    protected PasswordResetLinkRepository $passwordResetLinkRepo;
    protected UserPasswordHasherInterface $passwordEncoder;

    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        Security                    $security,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository              $userRepository,
        PasswordResetLinkRepository $passwordResetLinkRepository
    ) {
        $this->security = $security;
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
        $resetLink = $this->passwordResetLinkRepo->getByCode($command->getHash());
        if (!$resetLink->isActive() && $resetLink->isUsed()) {
            return self::LINK_NOT_ACTIVE;
        }
        // -----------------------------------------------------------
        if (!$command->isPasswordValid()) {
            throw new \Exception('Password is not valid.');
        }
        $user = $this->userRepo->getById($resetLink->getUser()->getId());
        $user->setPassword($this->passwordEncoder->hashPassword($user, $command->getNewPlainPassword()));
        $userId = $this->userRepo->save($user);
        $resetLink->useLink($command->getHash());
        $resetLinkId = $this->passwordResetLinkRepo->save($resetLink);
        // -----------------------------------------------------------
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
