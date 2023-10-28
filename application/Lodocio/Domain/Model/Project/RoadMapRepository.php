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

namespace App\Lodocio\Domain\Model\Project;

use Symfony\Component\Uid\Uuid;

interface RoadMapRepository
{
    public function nextIdentity(): Uuid;

    public function save(RoadMap $model): ?int;

    public function delete(RoadMap $model): bool;

    public function getById(int $id): RoadMap;

    public function getByUuid(Uuid $uuid): RoadMap;

    public function getByProject(DocProject $project): array;

}
