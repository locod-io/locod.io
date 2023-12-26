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

use App\Lodocio\Domain\Model\Project\DocProject;
use Symfony\Component\Uid\Uuid;

interface WikiRepository
{
    public function nextIdentity(): Uuid;

    public function save(Wiki $model): ?int;

    public function delete(Wiki $model): bool;

    public function getById(int $id): Wiki;

    public function getByUuid(Uuid $uuid): Wiki;

    public function getAll(): array;

    public function getByProject(DocProject $project): array;

    public function getNextArtefactId(DocProject $project): int;

    public function getMaxSequence(DocProject $project): int;

    public function getBySlug(string $slug): Wiki;

}
