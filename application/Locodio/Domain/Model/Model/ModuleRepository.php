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

interface ModuleRepository extends ArtefactRepositoryInterface
{
    public function save(Module $model): ?int;

    public function delete(Module $model): bool;

    public function getById(int $id): Module;

    public function getByUuid(Uuid $uuid): Module;

    public function getMaxSequence(Project $project): Module;

    public function getByDocumentor(Documentor $documentor): Module;

    /**  @return Module[] */
    public function getByProject(Project $project): array;
}
