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

namespace App\Locodio\Application\Command\Model\OrderRelation;

use App\Locodio\Domain\Model\Model\RelationRepository;

class OrderRelationHandler
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

    public function go(OrderRelation $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $field = $this->relationRepo->getById($sequenceId);
            $field->setSequence($sequence);
            $this->relationRepo->save($field);
            $sequence++;
        }

        return true;
    }
}
