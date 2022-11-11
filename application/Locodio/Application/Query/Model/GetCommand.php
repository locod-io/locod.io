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

namespace App\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\CommandRM;
use App\Locodio\Domain\Model\Model\CommandRepository;

class GetCommand
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected CommandRepository $commandRepo
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

    public function ById(int $id): CommandRM
    {
        $model = $this->commandRepo->getById($id);
        return CommandRM::hydrateFromModel($model, true);
    }
}
