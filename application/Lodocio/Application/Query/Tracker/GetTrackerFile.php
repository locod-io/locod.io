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

namespace App\Lodocio\Application\Query\Tracker;

use App\Lodocio\Application\Query\Tracker\ReadModel\TrackerNodeFileReadModel;
use App\Lodocio\Domain\Model\Tracker\TrackerNodeFileRepository;
use Symfony\Component\Uid\Uuid;

class GetTrackerFile
{
    public function __construct(
        protected TrackerNodeFileRepository $fileRepository
    ) {

    }

    public function ById(int $id): TrackerNodeFileReadModel
    {
        return TrackerNodeFileReadModel::hydrateFromModel($this->fileRepository->getById($id));
    }

    public function ByUuid(string $uuid): TrackerNodeFileReadModel
    {
        return TrackerNodeFileReadModel::hydrateFromModel($this->fileRepository->getByUuid(Uuid::fromString($uuid)));
    }

}
