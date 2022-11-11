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

namespace App\Locodio\Application\Command\Model\ChangeDomainModel;

use App\Locodio\Domain\Model\Model\DomainModelRepository;

class ChangeDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeDomainModel $command): bool
    {
        $model = $this->domainModelRepo->getById($command->getId());
        $model->change($command->getName(), $command->getNamespace(), $command->getRepository());
        $this->domainModelRepo->save($model);

        return true;
    }
}
