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

interface AttributeRepository extends ArtefactRepositoryInterface
{
    public function save(Attribute $model): ?int;

    public function delete(Attribute $model): bool;

    public function getById(int $id): Attribute;

    public function getByUuid(string $uuid): Attribute;

    public function getMaxSequence(DomainModel $model): Attribute;

    /** @return Attribute[] */
    public function getByDomainModel(DomainModel $domainModel): array;
}
