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

interface RoadMapItemRepository
{
    public function nextIdentity(): Uuid;

    public function save(RoadMapItem $model): ?int;

    public function delete(RoadMapItem $model): bool;

    public function getById(int $id): RoadMapItem;

    public function getByUuid(Uuid $uuid): RoadMapItem;

    public function getByRoadMap(RoadMap $roadMap): array;

}
