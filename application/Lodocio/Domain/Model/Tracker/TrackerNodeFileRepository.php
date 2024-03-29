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

interface TrackerNodeFileRepository
{
    public function nextIdentity(): Uuid;

    public function save(TrackerNodeFile $model): ?int;

    public function delete(TrackerNodeFile $model): bool;

    public function getById(int $id): TrackerNodeFile;

    public function getByUuid(Uuid $uuid): TrackerNodeFile;

    public function findByTrackerNodeAndName(TrackerNode $node, string $name): ?TrackerNodeFile;

    public function getAll(): array;

    public function getByTrackerNode(TrackerNode $trackerNode): array;

    public function getNextArtefactId(Tracker $tracker): int;

}
