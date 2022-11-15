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

interface FieldRepository
{
    public function nextIdentity(): Uuid;

    public function save(Field $model): ?int;

    public function delete(Field $model): bool;

    public function getById(int $id): Field;

    public function getByUuid(string $uuid): Field;

    public function getMaxSequence(DomainModel $model): Field;

    /** @return Field[] */
    public function getByDomainModel(DomainModel $domainModel): array;
}
