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

interface WikiNodeFileRepository
{
    public function nextIdentity(): Uuid;

    public function save(WikiNodeFile $model): ?int;

    public function delete(WikiNodeFile $model): bool;

    public function getById(int $id): WikiNodeFile;

    public function getByUuid(Uuid $uuid): WikiNodeFile;

    public function findByWikiNodeAndName(WikiNode $wikiNode, string $name): ?WikiNodeFile;

    public function getAll(): array;

    public function getByWikiNode(WikiNode $wikiNode): array;

    public function getNextArtefactId(Wiki $wiki): int;

}
