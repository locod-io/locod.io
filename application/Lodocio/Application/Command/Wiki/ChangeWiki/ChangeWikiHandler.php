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

namespace App\Lodocio\Application\Command\Wiki\ChangeWiki;

use App\Lodocio\Domain\Model\Wiki\WikiRepository;

class ChangeWikiHandler
{
    public function __construct(
        protected WikiRepository $wikiRepo
    ) {
    }

    public function go(ChangeWiki $command): bool
    {
        $model = $this->wikiRepo->getById($command->getId());
        $model->change(
            $command->getName(),
            $command->getCode(),
            '#'.$command->getColor(),
            $command->getDescription(),
            $command->getRelatedTeams(),
            $command->getSlug(),
            $command->isPublic(),
            $command->showOnlyFinalNodes()
        );
        $id = $this->wikiRepo->save($model);
        return true;
    }
}
