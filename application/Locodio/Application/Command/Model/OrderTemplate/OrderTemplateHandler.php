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

namespace App\Locodio\Application\Command\Model\OrderTemplate;

use App\Locodio\Domain\Model\Model\TemplateRepository;

class OrderTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected TemplateRepository $templateRepo)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderTemplate $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->templateRepo->getById($sequenceId);
            $model->setSequence($sequence);
            $this->templateRepo->save($model);
            $sequence++;
        }

        return true;
    }
}
