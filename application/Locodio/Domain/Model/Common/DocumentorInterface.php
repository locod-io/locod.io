<?php

declare(strict_types=1);

namespace App\Locodio\Domain\Model\Common;

use App\Locodio\Domain\Model\Model\Documentor;

interface DocumentorInterface
{
    public function document(Documentor $documentor): void;

    public function getDocumentor(): ?Documentor;
}
