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

namespace App\Lodocio\Application\Command\Wiki\DeleteWikiNodeStatus;

use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;

class DeleteWikiNodeStatusHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected WikiNodeRepository       $wikiNodeRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteWikiNodeStatus $command): bool
    {
        $status = $this->wikiNodeStatusRepository->getById($command->getId());
        $usages = $this->wikiNodeRepository->countByStatus($status->getId());
        if ($usages > 0) {
            return false;
        }
        $this->wikiNodeStatusRepository->delete($status);

        return true;
    }
}
