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

use App\Locodio\Domain\Model\User\User;
use Symfony\Component\Uid\Uuid;

interface MasterTemplateRepository
{
    public function nextIdentity(): Uuid;

    public function save(MasterTemplate $model): ?int;

    public function delete(MasterTemplate $model): bool;

    public function getById(int $id): MasterTemplate;

    public function getByUuid(string $uuid): MasterTemplate;

    /** @return MasterTemplate[] */
    public function getByUser(User $user): array;

    /** @return MasterTemplate[] */
    public function getPublicTemplates(): array;
}
