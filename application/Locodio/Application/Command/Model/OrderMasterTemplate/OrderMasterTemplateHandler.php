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

namespace App\Locodio\Application\Command\Model\OrderMasterTemplate;

use App\Locodio\Domain\Model\Model\MasterTemplateRepository;

class OrderMasterTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected MasterTemplateRepository $masterTemplateRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderMasterTemplate $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->masterTemplateRepo->getById($sequenceId);
            $model->setSequence($sequence);
            $this->masterTemplateRepo->save($model);
            $sequence++;
        }

        return true;
    }
}
