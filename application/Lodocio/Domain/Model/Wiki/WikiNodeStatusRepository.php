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

interface WikiNodeStatusRepository
{
    public function nextIdentity(): Uuid;

    public function save(WikiNodeStatus $model): ?int;

    public function delete(WikiNodeStatus $model): bool;

    public function getById(int $id): WikiNodeStatus;

    public function getByUuid(Uuid $uuid): WikiNodeStatus;

    public function getAll(): array;

    public function getByWiki(Wiki $wiki): array;

    public function getNextArtefactId(Wiki $wiki): int;

    public function getMaxSequence(Wiki $wiki): int;

    public function findFirstStatus(Wiki $wiki): ?WikiNodeStatus;

    public function findFinalStatus(Wiki $wiki): ?WikiNodeStatus;

}
