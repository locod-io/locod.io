<?php

declare(strict_types=1);

namespace App\Locodio\Application\Command\User\ChangeOrganisationUserRoles;

trait ChangeOrganisationUserRolesTrait
{
    /**
     * @throws \Exception
     */
    public function changeOrganisationUserRoles(\stdClass $jsonCommand): bool
    {
        $command = ChangeOrganisationUserRoles::hydrateFromJson($jsonCommand);

        $this->permission->CheckRole(['ROLE_ORGANISATION_ADMIN']);
        $this->permission->CheckOrganisationId($command->getOrganisationId());

        $handler = new ChangeOrganisationUserRolesHandler(
            $this->userRepository,
            $this->organisationRepository,
            $this->organisationUserRepository
        );
        $result = $handler->changeRoles($command);
        $this->entityManager->flush();

        return $result;
    }

}
