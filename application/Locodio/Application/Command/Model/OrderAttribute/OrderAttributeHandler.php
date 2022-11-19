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

namespace App\Locodio\Application\Command\Model\OrderAttribute;

use App\Locodio\Domain\Model\Model\AttributeRepository;

class OrderAttributeHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected AttributeRepository $attributeRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderAttribute $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $field = $this->attributeRepo->getById($sequenceId);
            $field->setSequence($sequence);
            $this->attributeRepo->save($field);
            $sequence++;
        }
        return true;
    }
}
