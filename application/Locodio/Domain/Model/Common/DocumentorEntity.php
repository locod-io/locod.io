<?php

namespace App\Locodio\Domain\Model\Common;

use App\Locodio\Domain\Model\Model\Documentor;
use Doctrine\ORM\Mapping as ORM;

trait DocumentorEntity
{
    #[ORM\OneToOne(targetEntity: "App\Locodio\Domain\Model\Model\Documentor", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Documentor $documentor = null;

    public function document(Documentor $documentor): void
    {
        $this->documentor = $documentor;
    }

    public function getDocumentor(): ?Documentor
    {
        return $this->documentor;
    }
}
