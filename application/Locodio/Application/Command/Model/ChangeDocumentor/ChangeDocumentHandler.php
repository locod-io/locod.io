<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Application\Command\Model\ChangeDocumentor;

use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;
use Symfony\Component\Security\Core\Security;

class ChangeDocumentHandler
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security              $security,
        protected DocumentorRepository  $documentorRepo,
        protected ModelStatusRepository $modelStatusRepo,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Commands
    // —————————————————————————————————————————————————————————————————————————

    public function go(ChangeDocumentor $command): bool
    {
        $model = $this->documentorRepo->getById($command->getId());
        $previousStatus = $model->getStatus();
        $status = $this->modelStatusRepo->getById($command->getStatusId());

        if (in_array($status->getId(), $previousStatus->getFlowOut())
            || $status->getId() === $previousStatus->getId()) {
            $finalStatus = $this->modelStatusRepo->getFinalByProject($status->getProject());

            $model->change($command->getDescription(), $status);

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

            $this->documentorRepo->save($model);
            return true;
        }
        return false;
    }

    public function goStatus(ChangeDocumentorStatus $command): bool
    {
        $model = $this->documentorRepo->getById($command->getId());
        $previousStatus = $model->getStatus();
        $status = $this->modelStatusRepo->getById($command->getStatusId());
        if (in_array($status->getId(), $previousStatus->getFlowOut())
            || $status->getId() === $previousStatus->getId()) {
            $finalStatus = $this->modelStatusRepo->getFinalByProject($status->getProject());

            $model->setStatus($status);

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

            $this->documentorRepo->save($model);
            return true;
        }
        return false;
    }
}
