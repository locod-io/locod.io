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

namespace App\Lodocio\Application\Command\Wiki\DeleteWiki;

use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;
use App\Lodocio\Infrastructure\Database\Wiki\WikiNodeRepository;

class DeleteWikiHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected WikiRepository           $wikiRepository,
        protected WikiNodeRepository       $wikiNodeRepository,
        protected WikiNodeGroupRepository  $wikiNodeGroupRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository,
    ) {
    }

    public function go(DeleteWiki $command): bool
    {
        $wiki = $this->wikiRepository->getById($command->getId());

        // if (count($wiki->getWikiNodes()) > 0) {
        //     return false;
        // }

        // -- delete all the related nodes
        foreach ($wiki->getWikiNodes() as $node) {
            $this->wikiNodeRepository->delete($node);
        }

        // -- delete all the selected groups
        foreach ($wiki->getWikiGroups() as $group) {
            $this->wikiNodeGroupRepository->delete($group);
        }

        // -- delete all the related status
        foreach ($wiki->getWikiNodeStatus() as $status) {
            $this->wikiNodeStatusRepository->delete($status);
        }

        // -- delete the wiki
        $this->wikiRepository->delete($wiki);

        return true;
    }

}
