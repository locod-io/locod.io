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

use App\Locodio\Domain\Model\User\User;
use Symfony\Component\Uid\Uuid;

interface OrganisationRepository
{
    public function nextIdentity(): Uuid;

    public function save(Organisation $model): ?int;

    public function getById(int $id): Organisation;

    public function getByUuid(Uuid $uuid): Organisation;

    public function getByUser(User $user): array;

    public function getAll(): array;
}
