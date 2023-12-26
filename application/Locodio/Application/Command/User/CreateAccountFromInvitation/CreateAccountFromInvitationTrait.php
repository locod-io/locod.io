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

namespace App\Locodio\Application\Command\User\CreateAccountFromInvitation;

trait CreateAccountFromInvitationTrait
{
    public function createAccountFromInvitation(CreateAccountFromInvitation $command): bool
    {
        $handler = new CreateAccountFromInvitationHandler(
            $this->userInvitationLinkRepository,
            $this->userRepository,
            $this->organisationRepository,
            $this->organisationUserRepository,
            $this->passwordEncoder,
        );
        $result = $handler->create($command);
        $this->entityManager->flush();

        return $result;
    }

}
