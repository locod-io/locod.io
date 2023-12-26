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

namespace App\Locodio\Application\Command\User\ValidateInvitation;

trait ValidationInvitationTrait
{
    public function validateInvitation(\stdClass $jsonCommand): bool
    {
        $command = ValidationInvitation::hydrateFromJson($jsonCommand);
        $handler = new ValidationInvitationHandler($this->userInvitationLinkRepository);
        $result = $handler->validate($command);
        $this->entityManager->flush();
        return $result;
    }
}
