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

namespace App\Lodocio\Application\Command\Wiki\ChangeNodeName;

use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;

class ChangeNodeNameHandler
{
    public function __construct(
        protected WikiNodeRepository $wikiNodeRepository
    ) {
    }

    public function go(ChangeNodeName $command): bool
    {
        $model = $this->wikiNodeRepository->getById($command->getId());
        if(!$model->getWikiNodeStatus()->isFinal()) {
            $model->setName($command->getName());
            $id = $this->wikiNodeRepository->save($model);
        }
        return true;
    }
}
