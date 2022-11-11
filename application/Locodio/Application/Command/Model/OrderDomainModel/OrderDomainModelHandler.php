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

namespace App\Locodio\Application\Command\Model\OrderDomainModel;

use App\Locodio\Domain\Model\Model\DomainModelRepository;

class OrderDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected DomainModelRepository $domainModelRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderDomainModel $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->domainModelRepo->getById($sequenceId);
            $model->setSequence($sequence);
            $this->domainModelRepo->save($model);
            $sequence++;
        }

        return true;
    }
}
