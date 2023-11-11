<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Domain\Model\Tracker;

use Symfony\Component\Uid\Uuid;

interface TrackerRelatedProjectDocumentRepository
{
    public function nextIdentity(): Uuid;

    public function save(TrackerRelatedProjectDocument $model): ?int;

    public function delete(TrackerRelatedProjectDocument $model): bool;

    public function getById(int $id): TrackerRelatedProjectDocument;

    public function getByUuid(Uuid $uuid): TrackerRelatedProjectDocument;

    public function getAll(): array;

}
