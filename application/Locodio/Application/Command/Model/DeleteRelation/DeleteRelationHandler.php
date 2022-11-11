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

namespace App\Locodio\Application\Command\Model\DeleteRelation;

use App\Locodio\Domain\Model\Model\RelationRepository;

class DeleteRelationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected RelationRepository $relationRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteRelation $command): bool
    {
        $relation = $this->relationRepo->getById($command->getId());
        $this->relationRepo->delete($relation);
        return true;
    }
}
