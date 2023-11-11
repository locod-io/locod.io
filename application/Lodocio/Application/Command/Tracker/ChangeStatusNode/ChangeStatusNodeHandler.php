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

namespace App\Lodocio\Application\Command\Tracker\ChangeStatusNode;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeRepository;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeStatusRepository;
use Symfony\Component\Security\Core\Security;

class ChangeStatusNodeHandler
{
    public function __construct(
        protected Security                    $security,
        protected TrackerNodeRepository       $trackerNodeRepository,
        protected TrackerNodeStatusRepository $trackerNodeStatusRepository
    ) {
    }

    public function go(ChangeStatusNode $command): bool
    {
        $model = $this->trackerNodeRepository->getById($command->getId());
        $status = $this->trackerNodeStatusRepository->getById($command->getTrackerNodeStatusId());
        $previousStatus = $model->getTrackerNodeStatus();

        if (in_array($status->getId(), $previousStatus->getFlowOut())
            || $status->getId() === $previousStatus->getId()) {

            $finalStatus = $this->trackerNodeStatusRepository->findFinalStatus($model->getTracker());
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

            $this->trackerNodeRepository->save($model);
            return true;
        }
        return false;


    }
}
