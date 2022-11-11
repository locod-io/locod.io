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

namespace App\Locodio\Application\Command\Model\ChangeEnumOption;

use App\Locodio\Domain\Model\Model\EnumOptionRepository;

class ChangeEnumOptionHandler
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

    public function go(ChangeEnumOption $command): bool
    {
        $enum = $this->enumOptionRepo->getById($command->getId());
        $enum->change($command->getCode(), $command->getValue());
        $this->enumOptionRepo->save($enum);

        return true;
    }
}
