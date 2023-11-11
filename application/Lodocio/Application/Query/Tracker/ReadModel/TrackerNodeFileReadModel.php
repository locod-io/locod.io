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

namespace App\Lodocio\Application\Query\Tracker\ReadModel;

use App\Lodocio\Domain\Model\Tracker\TrackerNodeFile;

class TrackerNodeFileReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected int    $id,
        protected string $uuid,
        protected int    $sequence,
        public int       $artefactId,
        public string    $name,
        protected string $originalFileName,
        protected string $srcPath,
        protected string $previewPath,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->uuid = $this->getUuid();
        $json->sequence = $this->getSequence();
        $json->artefactId = $this->getArtefactId();
        $json->name = $this->getName();
        $json->srcPath = $this->getSrcPath();
        $json->originalFileName = $this->getOriginalFileName();

        // $json->previewPath = $this->getPreviewPath();

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(TrackerNodeFile $model): self
    {
        return new self(
            $model->getId(),
            $model->getUuid()->toRfc4122(),
            $model->getSequence(),
            $model->getArtefactId(),
            $model->getName(),
            $model->getOriginalFileName(),
            $model->getSrcPath(),
            $model->getPreviewPath(),
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getSrcPath(): string
    {
        return $this->srcPath;
    }

    public function getPreviewPath(): string
    {
        return $this->previewPath;
    }
}
