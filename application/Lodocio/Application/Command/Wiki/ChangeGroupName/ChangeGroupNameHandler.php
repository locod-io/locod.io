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

namespace App\Lodocio\Application\Command\Wiki\ChangeGroupName;

use App\Lodocio\Domain\Model\Wiki\WikiNodeGroupRepository;

class ChangeGroupNameHandler
{
    public function __construct(
        protected WikiNodeGroupRepository $wikiNodeGroupRepository
    ) {
    }

    public function go(ChangeGroupName $command): bool
    {
        $model = $this->wikiNodeGroupRepository->getById($command->getId());
        $model->setName($command->getName());
        $id = $this->wikiNodeGroupRepository->save($model);
        return true;
    }
}
