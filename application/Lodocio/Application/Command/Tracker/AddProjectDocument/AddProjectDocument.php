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

namespace App\Lodocio\Application\Command\Tracker\AddProjectDocument;

use App\Lodocio\Application\Command\Tracker\ProjectDocumentType;

class AddProjectDocument
{

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectDocumentType $type,
        protected int                 $subjectId,
        protected string              $relatedProjectId,
        protected string              $content,
    )
    {
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from a json command
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            ProjectDocumentType::tryFrom($json->type),
            (int)$json->subjectId,
            $json->relatedProjectId,
            $json->content,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getType(): ProjectDocumentType
    {
        return $this->type;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getRelatedProjectId(): string
    {
        return $this->relatedProjectId;
    }

    public function getContent(): string
    {
        return $this->content;
    }


}