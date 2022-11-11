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

namespace App\Locodio\Application\Command\Model\OrderField;

use App\Locodio\Domain\Model\Model\FieldRepository;

class OrderFieldHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected FieldRepository $fieldRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderField $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $field = $this->fieldRepo->getById($sequenceId);
            $field->setSequence($sequence);
            $this->fieldRepo->save($field);
            $sequence++;
        }

        return true;
    }
}
