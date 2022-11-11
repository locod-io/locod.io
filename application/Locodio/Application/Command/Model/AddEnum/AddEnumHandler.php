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

namespace App\Locodio\Application\Command\Model\AddEnum;

use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use App\Locodio\Domain\Model\Model\EnumOptionRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddEnumHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected EnumRepository        $enumRepo,
        protected EnumOptionRepository  $enumOptionRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddEnum $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());

        // shift all other domain models in the sequence --------------------

        $enums = $this->enumRepo->getByProject($project);
        foreach ($enums as $enum) {
            $enum->setSequence($enum->getSequence()+1);
            $this->enumRepo->save($enum);
        }

        // make the enum ----------------------------------------------------

        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $model = Enum::make(
            $project,
            $this->enumRepo->nextIdentity(),
            $domainModel,
            $command->getName()
        );
        $this->enumRepo->save($model);
        $enumOption = EnumOption::make($model, $this->enumOptionRepo->nextIdentity(), 'code', 'value');
        $this->enumOptionRepo->save($enumOption);

        return true;
    }
}
