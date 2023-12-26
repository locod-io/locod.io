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

namespace App\Lodocio\Application\Command\Wiki\DeleteWikiNode;

use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;

class DeleteWikiNodeHandler
{
    public function __construct(
        protected WikiNodeRepository $wikiNodeRepository
    ) {
    }

    public function go(DeleteWikiNode $command): bool
    {
        $wikiNode = $this->wikiNodeRepository->getById($command->getId());
        $this->wikiNodeRepository->delete($wikiNode);

        return true;
    }

}
