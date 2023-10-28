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

interface TemplateRepository extends BaseRepositoryInteface
{
    public function save(Template $model): ?int;

    public function delete(Template $model): bool;

    public function getById(int $id): Template;

    public function getByUuid(string $uuid): Template;

    /** @return Template[] */
    public function getByProject(Project $project): array;

    /** @return Template[] */
    public function getByMasterTemplate(MasterTemplate $masterTemplate): array;
}
