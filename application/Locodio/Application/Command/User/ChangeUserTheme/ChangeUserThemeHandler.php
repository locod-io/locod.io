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

namespace App\Locodio\Application\Command\User\ChangeUserTheme;

use App\Locodio\Domain\Model\User\InterfaceTheme;
use App\Locodio\Domain\Model\User\UserRepository;

class ChangeUserThemeHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository $userRepo
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeUserTheme $command): bool
    {
        $user = $this->userRepo->getById($command->getId());
        $user->setTheme(InterfaceTheme::from($command->getTheme()));
        $this->userRepo->save($user);
        return true;
    }
}
