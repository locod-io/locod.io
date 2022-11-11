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

namespace App\Locodio\Application\Command\Model\AddEnumOption;

use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;

class AddEnumOptionHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected EnumRepository        $enumRepo,
        protected EnumOptionRepository  $enumOptionRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddEnumOption $command): bool
    {
        $enum = $this->enumRepo->getById($command->getEnumId());
        $option = EnumOption::make(
            $enum,
            $this->enumOptionRepo->nextIdentity(),
            $command->getCode(),
            $command->getValue()
        );
        $lastSequence = $this->enumOptionRepo->getMaxSequence($enum)->getSequence();
        $option->setSequence($lastSequence++);
        $this->enumOptionRepo->save($option);

        return true;
    }
}
