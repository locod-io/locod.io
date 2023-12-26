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

use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisation;
use App\Locodio\Application\Command\Organisation\AddOrganisation\AddOrganisationHandler;
use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisation;
use App\Locodio\Application\Command\Organisation\ChangeOrganisation\ChangeOrganisationHandler;
use App\Locodio\Application\Command\Organisation\OrderOrganisation\OrderOrganisation;
use App\Locodio\Application\Command\Organisation\OrderOrganisation\OrderOrganisationHandler;

trait organisation_organisation_command
{
    public function addOrganisation(\stdClass $jsonCommand): bool
    {
        $command = AddOrganisation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckUserId($command->getUserId());

        $handler = new AddOrganisationHandler($this->userRepository, $this->organisationRepository, $this->organisationUserRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function orderOrganisations(\stdClass $jsonCommand): bool
    {
        $command = OrderOrganisation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckOrganisationIds($command->getSequence());

        $handler = new OrderOrganisationHandler($this->organisationRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }

    public function changeOrganisation(\stdClass $jsonCommand): bool
    {
        $command = ChangeOrganisation::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckOrganisationId($command->getId());

        $handler = new ChangeOrganisationHandler($this->organisationRepository);
        $result = $handler->go($command);
        $this->entityManager->flush();
        return $result;
    }
}
