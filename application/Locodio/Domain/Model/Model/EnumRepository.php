<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Organisation\Project;
use Symfony\Component\Uid\Uuid;

interface EnumRepository
{
    public function nextIdentity(): Uuid;

    public function save(Enum $model): ?int;

    public function delete(Enum $model): bool;

    public function getById(int $id): Enum;

    public function getByUuid(string $uuid): Enum;

    /** @return Enum[] */
    public function getByProject(Project $project): array;
}
