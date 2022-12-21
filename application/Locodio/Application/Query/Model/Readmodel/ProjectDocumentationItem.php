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

namespace App\Locodio\Application\Query\Model\Readmodel;

class ProjectDocumentationItem implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int                         $id,
        protected string                      $label,
        protected int                         $level,
        protected string                      $levelLabel,
        protected string                      $type,
        protected string                      $typeCode,
        protected ?DocumentationItemInterface $item = null,
        protected ?string                     $documentorImageData = null,
        protected ?string                     $documentorImageOrientation = null,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->label = $this->getLabel();
        $json->level = $this->getLevel();
        $json->labelLevel = $this->getLevelLabel();
        $json->type = $this->getType();
        $json->typeCode = $this->getTypeCode();
        $json->item = $this->getItem();
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setDocumentorImageData(string $documentorImageData, string $orientation): void
    {
        $this->documentorImageData = $documentorImageData;
        $this->documentorImageOrientation = $orientation;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getLevelLabel(): string
    {
        return $this->levelLabel;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getItem(): ?DocumentationItemInterface
    {
        return $this->item;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTypeCode(): string
    {
        return $this->typeCode;
    }

    public function getDocumentorImageData(): ?string
    {
        return $this->documentorImageData;
    }

    public function getDocumentorImageOrientation(): ?string
    {
        return $this->documentorImageOrientation;
    }
}
