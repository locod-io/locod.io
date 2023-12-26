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

namespace App\Lodocio\Application\Command\Wiki\DeleteWikiNodeGroup;

use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;

class DeleteWikiNodeGroupHandler
{
    public function __construct(
        protected WikiRepository          $wikiRepository,
        protected WikiNodeRepository      $wikiNodeRepository,
        protected WikiNodeGroupRepository $wikiNodeGroupRepository,
    ) {
    }

    public function go(DeleteWikiNodeGroup $command): bool
    {
        $wikiGroup = $this->wikiNodeGroupRepository->getById($command->getId());
        $result = $this->wikiNodeGroupRepository->delete($wikiGroup);
        return true;
    }
}
