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

namespace App\Locodio\Application\Command\Organisation\OrderOrganisation;

use App\Locodio\Domain\Model\Organisation\OrganisationRepository;

class OrderOrganisationHandler
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

    public function go(OrderOrganisation $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->organisationRepo->getById($sequenceId);
            $model->setSequence($sequence);
            $this->organisationRepo->save($model);
            $sequence++;
        }

        return true;
    }
}
