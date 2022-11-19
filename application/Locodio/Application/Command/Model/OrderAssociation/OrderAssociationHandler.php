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

namespace App\Locodio\Application\Command\Model\OrderAssociation;

use App\Locodio\Domain\Model\Model\AssociationRepository;

class OrderAssociationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected AssociationRepository $associationRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderAssociation $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $field = $this->associationRepo->getById($sequenceId);
            $field->setSequence($sequence);
            $this->associationRepo->save($field);
            $sequence++;
        }
        return true;
    }
}
