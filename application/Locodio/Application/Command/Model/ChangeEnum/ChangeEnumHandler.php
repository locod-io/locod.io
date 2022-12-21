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

namespace App\Locodio\Application\Command\Model\ChangeEnum;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class ChangeEnumHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected EnumRepository        $enumRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeEnum $command): bool
    {
        $enum = $this->enumRepo->getById($command->getId());
        if (ModelFinalChecker::isFinalState($enum->getDocumentor())) {
            return false;
        }

        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $enum->change($domainModel, $command->getName(), $command->getNamespace());
        $this->enumRepo->save($enum);

        return true;
    }
}
