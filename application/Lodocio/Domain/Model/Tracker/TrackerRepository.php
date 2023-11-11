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

use App\Lodocio\Domain\Model\Project\DocProject;
use Symfony\Component\Uid\Uuid;

interface TrackerRepository
{
    public function nextIdentity(): Uuid;

    public function save(Tracker $model): ?int;

    public function delete(Tracker $model): bool;

    public function getById(int $id): Tracker;

    public function getByUuid(Uuid $uuid): Tracker;

    public function getAll(): array;

    public function getByProject(DocProject $project): array;

    public function getNextArtefactId(DocProject $project): int;

    public function getMaxSequence(DocProject $project): int;

}
