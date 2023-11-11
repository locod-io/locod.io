<?php

namespace App\Lodocio\Domain\Model\Tracker;

use Doctrine\ORM\Mapping as ORM;

trait ProjectDocumentTrait
{
    #[ORM\OneToOne(targetEntity: "App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocument", fetch: "EXTRA_LAZY")]
    #[ORM\JoinColumn(nullable: true)]
    private ?TrackerRelatedProjectDocument $relatedProjectDocument;

    public function setRelatedProjectDocument(?TrackerRelatedProjectDocument $trackerRelatedProjectDocument): void
    {
        $this->relatedProjectDocument = $trackerRelatedProjectDocument;
    }

    public function getRelatedProjectDocument(): ?TrackerRelatedProjectDocument
    {
        return $this->relatedProjectDocument;
    }

}
