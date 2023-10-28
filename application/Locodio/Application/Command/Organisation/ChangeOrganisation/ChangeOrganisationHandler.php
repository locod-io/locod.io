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

namespace App\Locodio\Application\Command\Organisation\ChangeOrganisation;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;

class ChangeOrganisationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected OrganisationRepository $organisationRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeOrganisation $command): bool
    {
        $organisation = $this->organisationRepo->getById($command->getId());
        $organisation->change(
            $command->getName(),
            $command->getCode(),
            '#' . $command->getColor(),
            $command->getLinearApiKey(),
        );
        $this->organisationRepo->save($organisation);

        return true;
    }
}
