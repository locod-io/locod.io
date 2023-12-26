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

namespace App\Locodio\Application\Command\User\InviteUser;

use App\Locodio\Domain\Model\User\UserRole;

trait InviteUserTrait
{
    /**
     * @throws \Exception
     */
    public function inviteUserToOrganisation(\stdClass $jsonCommand): bool
    {
        $command = InviteUser::hydrateFromJson($jsonCommand);
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_ADMIN->value]);
        $this->permission->CheckOrganisationId($command->getOrganisationId());

        $handler = new InviteUserHandler(
            $this->userRepository,
            $this->organisationRepository,
            $this->organisationUserRepository,
            $this->userInvitationLinkRepository,
            $this->mailer,
            $this->translator,
            $this->twig,
        );
        $result = $handler->invite($command);
        $this->entityManager->flush();

        return $result;
    }
}
