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

namespace App\Locodio\Application\Command\User\ChangePassword;

use App\Locodio\Domain\Model\User\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordHandler
{
    // ——————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository              $userRepo,
        protected UserPasswordHasherInterface $passwordEncoder
    ) {
    }

    // ——————————————————————————————————————————————————————————————
    // Handler
    // ——————————————————————————————————————————————————————————————

    public function go(ChangePassword $command): bool
    {
        if (!$command->isPasswordValid()) {
            throw new \Exception('Password is not valid.');
        }
        $user = $this->userRepo->getById($command->getUserId());
        $user->setPassword($this->passwordEncoder->hashPassword($user, $command->getPassword1()));
        $this->userRepo->save($user);

        return true;
    }
}
