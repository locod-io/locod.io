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

namespace App\Locodio\Application\Command\Model\DeleteAssociation;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\AssociationRepository;

class DeleteAssociationHandler
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

    public function go(DeleteAssociation $command): bool
    {
        $relation = $this->associationRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($relation->getDomainModel()->getDocumentor())) {
            return false;
        }

        $this->associationRepo->delete($relation);
        return true;
    }
}
