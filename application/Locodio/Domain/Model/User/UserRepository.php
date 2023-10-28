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

namespace App\Locodio\Domain\Model\User;

use Symfony\Component\Uid\Uuid;

interface UserRepository
{
    public function nextIdentity(): Uuid;

    public function save(User $entity, bool $flush = false): ?int;

    public function getById(int $id): User;

    public function findById(int $id): ?User;

    public function getByEmail(string $email): User;

    public function getByUuid(Uuid $uuid): User;
}
