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

namespace App\Locodio\Application\Command\Model\DeleteModelStatus;

use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\ModelStatusRepository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class DeleteModelStatusHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ModelStatusRepository $modelStatusRepo,
        protected DocumentorRepository  $documentorRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function go(DeleteModelStatus $command): bool
    {
        $modelStatus = $this->modelStatusRepo->getById($command->getId());
        $usages = $this->documentorRepo->countByModelStatus($modelStatus->getId());
        if ($usages > 0) {
            return false;
        }
        $this->modelStatusRepo->delete($modelStatus);

        return true;
    }
}
