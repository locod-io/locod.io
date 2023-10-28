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

namespace App\Lodocio\Domain\Model\Project;

use Symfony\Component\Uid\Uuid;

interface ReleaseRepository
{
    public function nextIdentity(): Uuid;

    public function save(Release $model): ?int;

    public function delete(Release $model): bool;

    public function getById(int $id): Release;

    public function getByUuid(Uuid $uuid): Release;

    public function getByProject(DocProject $project): array;


}
