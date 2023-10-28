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

namespace App\Locodio\Domain\Model\Organisation;

use Symfony\Component\Uid\Uuid;

interface ProjectRepository
{
    public function nextIdentity(): Uuid;

    public function save(Project $model): ?int;

    public function getById(int $id): Project;

    public function getByUuid(Uuid $uuid): Project;

    public function getByOrganisation(Organisation $organisation): array;

    public function getAll(): array;

}
