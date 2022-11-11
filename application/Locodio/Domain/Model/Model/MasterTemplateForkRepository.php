<?php

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
