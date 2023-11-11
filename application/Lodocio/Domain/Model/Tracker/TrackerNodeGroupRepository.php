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

namespace App\Lodocio\Domain\Model\Tracker;

use Symfony\Component\Uid\Uuid;

interface TrackerNodeGroupRepository
{
    public function nextIdentity(): Uuid;

    public function save(TrackerNodeGroup $model): ?int;

    public function delete(TrackerNodeGroup $model): bool;

    public function getById(int $id): TrackerNodeGroup;

    public function getByUuid(Uuid $uuid): TrackerNodeGroup;
    public function findByUuid(Uuid $uuid): ?TrackerNodeGroup;

    public function getAll(): array;

    public function getByTracker(Tracker $tracker): array;

    public function getNextArtefactId(Tracker $project): int;

}
