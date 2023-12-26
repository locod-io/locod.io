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

namespace App\Lodocio\Domain\Model\Wiki;

use Symfony\Component\Uid\Uuid;

interface WikiRelatedProjectDocumentRepository
{
    public function nextIdentity(): Uuid;

    public function save(WikiRelatedProjectDocument $model): ?int;

    public function delete(WikiRelatedProjectDocument $model): bool;

    public function getById(int $id): WikiRelatedProjectDocument;

    public function getByUuid(Uuid $uuid): WikiRelatedProjectDocument;

    public function getAll(): array;

}
