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

use Symfony\Component\Uid\Uuid;

interface EnumOptionRepository
{
    public function nextIdentity(): Uuid;

    public function save(EnumOption $model): ?int;

    public function delete(EnumOption $model): bool;

    public function getById(int $id): EnumOption;

    public function getByUuid(string $uuid): EnumOption;

    public function getMaxSequence(Enum $domainModel): EnumOption;

    /** @return EnumOption[] */
    public function getByEnum(Enum $enum): array;
}
