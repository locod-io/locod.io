<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Wiki\OrderWikiNodeStatus;

use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;

class OrderWikiNodeStatusHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(protected WikiNodeStatusRepository $wikiNodeStatusRepository)
    {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(OrderWikiNodeStatus $command): bool
    {
        $sequence = 0;
        foreach ($command->getSequence() as $sequenceId) {
            $model = $this->wikiNodeStatusRepository->getById($sequenceId);
            $model->setSequence($sequence);
            $this->wikiNodeStatusRepository->save($model);
            $sequence++;
        }
        return true;
    }
}
