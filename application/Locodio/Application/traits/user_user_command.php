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

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Command\User\ChangeUserTheme\ChangeUserTheme;
use App\Locodio\Application\Command\User\ChangeUserTheme\ChangeUserThemeHandler;
use App\Locodio\Domain\Model\User\UserRole;

trait user_user_command
{
    /**
     * @throws \Exception
     */
    public function changeTheme(\stdClass $jsonCommand): bool
    {
        $command = ChangeUserTheme::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckUserId($command->getId());
        $handler = new ChangeUserThemeHandler($this->userRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();

        return $result;
    }
}
