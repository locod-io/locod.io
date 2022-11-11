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

interface PasswordResetLinkRepository
{
    public function nextIdentity(): Uuid;

    public function save(PasswordResetLink $model): ?int;

    public function getById(int $id): PasswordResetLink;

    public function getByUuid(Uuid $uuid): PasswordResetLink;

    public function getByCode(string $code): PasswordResetLink;

    public function getByUser(int $userId): array;
}
