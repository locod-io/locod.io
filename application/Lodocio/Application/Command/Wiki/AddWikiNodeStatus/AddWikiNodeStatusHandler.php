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

namespace App\Lodocio\Application\Command\Wiki\AddWikiNodeStatus;

use App\Lodocio\Domain\Model\Wiki\WikiNodeStatus;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use App\Lodocio\Domain\Model\Wiki\WikiRepository;

class AddWikiNodeStatusHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected WikiRepository           $wikiRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(AddWikiNodeStatus $command): bool
    {
        $wiki = $this->wikiRepository->getById($command->getWikiId());

        if ($command->isFinal() || $command->isStart()) {
            $wikiStatus = $this->wikiNodeStatusRepository->getByWiki($wiki);
            foreach ($wikiStatus as $status) {
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

        $model = WikiNodeStatus::make(
            $wiki,
            $this->wikiNodeStatusRepository->nextIdentity(),
            $command->getName(),
            $command->getColor(),
            $command->isStart(),
            $command->isFinal(),
        );

        $model->setSequence($this->wikiNodeStatusRepository->getMaxSequence($wiki));
        $model->setArtefactId($this->wikiNodeStatusRepository->getNextArtefactId($wiki));
        $this->wikiNodeStatusRepository->save($model);

        return true;
    }
}
