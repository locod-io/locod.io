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

namespace App\Lodocio\Application\Command\Wiki\ChangeStatusNode;

use App\Lodocio\Domain\Model\Wiki\WikiNodeRepository;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository;
use Symfony\Component\Security\Core\Security;

class ChangeStatusNodeHandler
{
    public function __construct(
        protected Security                 $security,
        protected WikiNodeRepository       $wikiNodeRepository,
        protected WikiNodeStatusRepository $wikiNodeStatusRepository
    ) {
    }

    public function go(ChangeStatusNode $command): bool
    {
        $model = $this->wikiNodeRepository->getById($command->getId());
        $status = $this->wikiNodeStatusRepository->getById($command->getWikiNodeStatusId());
        $previousStatus = $model->getWikiNodeStatus();

        if (in_array($status->getId(), $previousStatus->getFlowOut())
            || $status->getId() === $previousStatus->getId()) {

            $finalStatus = $this->wikiNodeStatusRepository->findFinalStatus($model->getWiki());
            $model->setStatus($status);
            if (!is_null($finalStatus)) {
                // -- change to final state, tag final state change
                if ($status->getId() === $finalStatus->getId() &&
                    $status->getId() !== $previousStatus->getId()) {
                    if (is_null($this->security->getUser())) {
                        $finalBy = 'local_dev_user';
                    } else {
                        $finalBy = $this->security->getUser()->getUserIdentifier();
                    }
                    $model->finalize($finalBy);
                } elseif ($status->getId() !== $previousStatus->getId()
                    && $status->getId() !== $finalStatus->getId()) {
                    $model->deFinalize();
                }
            } else {
                $model->deFinalize();
            }

            $this->wikiNodeRepository->save($model);
            return true;
        }
        return false;


    }
}
