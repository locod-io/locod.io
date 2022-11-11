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

namespace App\Locodio\Application\Command\Model\OrderEnumOption;

use App\Locodio\Domain\Model\Model\EnumOptionRepository;

class OrderEnumOptionHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected EnumOptionRepository $enumOptionRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderEnumOption $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $option = $this->enumOptionRepo->getById($sequenceId);
            $option->setSequence($sequence);
            $this->enumOptionRepo->save($option);
            $sequence++;
        }

        return true;
    }
}
