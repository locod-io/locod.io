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

interface WikiNodeRepository
{
    public function nextIdentity(): Uuid;

    public function save(WikiNode $model): ?int;

    public function delete(WikiNode $model): bool;

    public function getById(int $id): WikiNode;

    public function getByUuid(Uuid $uuid): WikiNode;

    public function findByUuid(Uuid $uuid): ?WikiNode;

    public function getAll(): array;

    public function getByWiki(Wiki $wiki): array;

    public function getNextArtefactId(Wiki $project): int;

    public function countByStatus(int $wikiNodeStatusId): int;

}
