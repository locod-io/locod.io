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

namespace App\Lodocio\Application\Query\Wiki;

use App\Lodocio\Application\Query\Wiki\ReadModel\WikiNodeFileReadModel;
use App\Lodocio\Domain\Model\Wiki\WikiNodeFileRepository;
use Symfony\Component\Uid\Uuid;

class GetWikiFile
{
    public function __construct(
        protected WikiNodeFileRepository $fileRepository
    ) {

    }

    public function ById(int $id): WikiNodeFileReadModel
    {
        return WikiNodeFileReadModel::hydrateFromModel($this->fileRepository->getById($id));
    }

    public function ByUuid(string $uuid): WikiNodeFileReadModel
    {
        return WikiNodeFileReadModel::hydrateFromModel($this->fileRepository->getByUuid(Uuid::fromString($uuid)));
    }

}
