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

interface MasterTemplateForkRepository
{
    public function nextIdentity(): Uuid;

    public function save(MasterTemplateFork $model): ?int;

    public function delete(MasterTemplateFork $model): bool;

    public function getById(int $id): MasterTemplateFork;

    public function getByUuid(Uuid $uuid): MasterTemplateFork;
}
