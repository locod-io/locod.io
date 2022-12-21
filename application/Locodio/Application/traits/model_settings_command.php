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

use App\Locodio\Application\Command\Model\ChangeModelSettings\ChangeModelSettings;
use App\Locodio\Application\Command\Model\ChangeModelSettings\ChangeModelSettingsHandler;

trait model_settings_command
{
    public function changeModelSettings(\stdClass $jsonCommand): bool
    {
        $command = ChangeModelSettings::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckProjectId($command->getProjectId());

        $commandHandler = new ChangeModelSettingsHandler(
            $this->projectRepo,
            $this->modelSettingsRepo
        );
        $result = $commandHandler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
