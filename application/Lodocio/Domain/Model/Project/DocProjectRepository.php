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

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use Symfony\Component\Uid\Uuid;

interface DocProjectRepository
{
    public function nextIdentity(): Uuid;

    public function save(DocProject $model): ?int;

    public function getById(int $id): DocProject;

    public function getByUuid(Uuid $uuid): DocProject;

    public function getByProject(Project $project): DocProject;

    public function findByProject(Project $project): ?DocProject;

    public function getByOrganisation(Organisation $organisation): array;
}
