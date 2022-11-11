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

namespace App\Locodio\Application\Command\User\ChangeProfile;

use App\Locodio\Domain\Model\User\UserRepository;

class ChangeProfileHandler
{
    // ——————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository $userRepo,
    ) {
    }

    // ——————————————————————————————————————————————————————————————
    // Handler
    // ——————————————————————————————————————————————————————————————

    public function go(ChangeProfile $command): bool
    {
        $user = $this->userRepo->getById($command->getUserId());
        $user->change($command->getFirstname(), $command->getLastname(), '#'.$command->getColor());
        $this->userRepo->save($user);

        return true;
    }
}
