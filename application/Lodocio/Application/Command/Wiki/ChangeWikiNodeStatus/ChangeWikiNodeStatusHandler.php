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

namespace App\Lodocio\Application\Command\Wiki\ChangeWikiNodeStatus;

use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;

class ChangeWikiNodeStatusHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected WikiNodeStatusRepository $wikiNodeStatusRepository,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeWikiNodeStatus $command): bool
    {
        $model = $this->wikiNodeStatusRepository->getById($command->getId());

        if ($command->isFinal() || $command->isStart()) {
            $modelStatus = $this->wikiNodeStatusRepository->getByWiki($model->getWiki());
            foreach ($modelStatus as $status) {
                if ($status->getId() !== $model->getId()) {
                    if ($command->isFinal()) {
                        $status->deFinalize();
                        $this->wikiNodeStatusRepository->save($status);
                    }
                    if ($command->isStart()) {
                        $status->deStarterize();
                        $this->wikiNodeStatusRepository->save($status);
                    }
                }
            }
        }

        $model->change(
            $command->getName(),
            $command->getColor(),
            $command->isStart(),
            $command->isFinal(),
        );
        $this->wikiNodeStatusRepository->save($model);
        return true;
    }
}
