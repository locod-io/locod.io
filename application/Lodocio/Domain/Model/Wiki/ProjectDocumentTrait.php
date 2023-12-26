<?php

namespace App\Lodocio\Domain\Model\Wiki;

use Doctrine\ORM\Mapping as ORM;

trait ProjectDocumentTrait
{
    #[ORM\OneToOne(targetEntity: "App\Lodocio\Domain\Model\Wiki\WikiRelatedProjectDocument", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    private ?WikiRelatedProjectDocument $relatedProjectDocument;

    public function setRelatedProjectDocument(?WikiRelatedProjectDocument $WikiRelatedProjectDocument): void
    {
        $this->relatedProjectDocument = $WikiRelatedProjectDocument;
    }

    public function getRelatedProjectDocument(): ?WikiRelatedProjectDocument
    {
        return $this->relatedProjectDocument;
    }

}
