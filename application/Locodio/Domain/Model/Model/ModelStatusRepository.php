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

interface ModelStatusRepository
{
    public function nextIdentity(): Uuid;

    public function save(ModelStatus $model): ?int;

    public function delete(ModelStatus $model): bool;

    public function getById(int $id): ModelStatus;

    public function getByUuid(Uuid $uuid): ModelStatus;

    public function getMaxSequence(Project $project): ModelStatus;

    public function getStartByProject(Project $project): ModelStatus;

    public function getFinalByProject(Project $project): ModelStatus;

    /**  @return ModelStatus[] */
    public function getByProject(Project $project): array;
}
