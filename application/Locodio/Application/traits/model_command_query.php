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

use App\Locodio\Application\Query\Model\GetCommand;
use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Domain\Model\User\UserRole;

trait model_command_query
{
    public function getCommandById(int $id): CommandRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckCommandId($id);

        $GetCommand = new GetCommand($this->commandRepo);
        return $GetCommand->ById($id);
    }
}
