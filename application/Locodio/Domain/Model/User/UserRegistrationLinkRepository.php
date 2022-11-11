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

interface UserRegistrationLinkRepository
{
    public function nextIdentity(): Uuid;

    public function save(UserRegistrationLink $model): ?int;

    public function delete(UserRegistrationLink $model): bool;

    public function getById(int $id): UserRegistrationLink;

    public function getByUuid(Uuid $uuid): UserRegistrationLink;

    public function getByCode(string $code): UserRegistrationLink;
}
