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

interface AssociationRepository
{
    public function nextIdentity(): Uuid;

    public function save(Association $model): ?int;

    public function delete(Association $model): bool;

    public function getById(int $id): Association;

    public function getByUuid(string $uuid): Association;

    /** @return Association[] */
    public function getByDomainModel(DomainModel $domainModel): array;
}
