<?php

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
