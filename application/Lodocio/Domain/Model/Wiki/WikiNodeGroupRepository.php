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

namespace App\Lodocio\Domain\Model\Wiki;

use Symfony\Component\Uid\Uuid;

interface WikiNodeGroupRepository
{
    public function nextIdentity(): Uuid;

    public function save(WikiNodeGroup $model): ?int;

    public function delete(WikiNodeGroup $model): bool;

    public function getById(int $id): WikiNodeGroup;

    public function getByUuid(Uuid $uuid): WikiNodeGroup;
    public function findByUuid(Uuid $uuid): ?WikiNodeGroup;

    public function getAll(): array;

    public function getByWiki(Wiki $wiki): array;

    public function getNextArtefactId(Wiki $project): int;

}
