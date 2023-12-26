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

namespace App\Lodocio\Application\Command\Wiki\ChangeNodeRelatedIssues;

use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;

class ChangeNodeRelatedIssuesHandler
{
    public function __construct(
        protected WikiNodeRepository $wikiNodeRepository
    ) {
    }

    public function go(ChangeNodeRelatedIssues $command): bool
    {
        $model = $this->wikiNodeRepository->getById($command->getId());
        $model->setRelatedIssues($command->getRelatedIssues());
        $id = $this->wikiNodeRepository->save($model);
        return true;
    }
}
