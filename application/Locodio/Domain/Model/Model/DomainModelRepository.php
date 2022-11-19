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

interface DomainModelRepository
{
    public function nextIdentity(): Uuid;

    public function save(DomainModel $model): ?int;

    public function delete(DomainModel $model): bool;

    public function getById(int $id): DomainModel;

    public function getByUuid(Uuid $uuid): DomainModel;

    /** @return DomainModel[] */
    public function getByProject(Project $project): array;
}
