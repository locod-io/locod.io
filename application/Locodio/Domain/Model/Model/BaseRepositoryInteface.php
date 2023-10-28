<?php

namespace App\Locodio\Domain\Model\Model;

use Symfony\Component\Uid\Uuid;

interface BaseRepositoryInteface
{
    public function nextIdentity(): Uuid;
}
