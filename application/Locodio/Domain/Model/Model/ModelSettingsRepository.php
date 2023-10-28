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

interface ModelSettingsRepository extends BaseRepositoryInteface
{
    public function save(ModelSettings $model): ?int;

    public function delete(ModelSettings $model): bool;

    public function getById(int $id): ModelSettings;

    public function getByUuid(Uuid $uuid): ModelSettings;

    public function getByProject(Project $project): ?ModelSettings;
}
