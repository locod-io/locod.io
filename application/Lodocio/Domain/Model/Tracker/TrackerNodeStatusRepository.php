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

interface TrackerNodeStatusRepository
{
    public function nextIdentity(): Uuid;

    public function save(TrackerNodeStatus $model): ?int;

    public function delete(TrackerNodeStatus $model): bool;

    public function getById(int $id): TrackerNodeStatus;

    public function getByUuid(Uuid $uuid): TrackerNodeStatus;

    public function getAll(): array;

    public function getByTracker(Tracker $tracker): array;

    public function getNextArtefactId(Tracker $tracker): int;

    public function getMaxSequence(Tracker $tracker): int;

    public function findFirstStatus(Tracker $tracker): ?TrackerNodeStatus;

    public function findFinalStatus(Tracker $tracker): ?TrackerNodeStatus;

}
