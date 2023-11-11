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

interface TrackerNodeRepository
{
    public function nextIdentity(): Uuid;

    public function save(TrackerNode $model): ?int;

    public function delete(TrackerNode $model): bool;

    public function getById(int $id): TrackerNode;

    public function getByUuid(Uuid $uuid): TrackerNode;

    public function findByUuid(Uuid $uuid): ?TrackerNode;

    public function getAll(): array;

    public function getByTracker(Tracker $tracker): array;

    public function getNextArtefactId(Tracker $project): int;

    public function countByStatus(int $trackerNodeStatusId): int;

}
