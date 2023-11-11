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

namespace App\Lodocio\Domain\Model\Common;

use Doctrine\ORM\Mapping as ORM;

trait ArtefactEntity
{
    #[ORM\Column(options: ["default" => 0])]
    private int $artefactId = 0;

    // ———————————————————————————————————————————————————————————————————
    // Getters and setters
    // ———————————————————————————————————————————————————————————————————

    public function getArtefactId(): int
    {
        return $this->artefactId;
    }

    public function setArtefactId(int $artefactId): void
    {
        $this->artefactId = $artefactId;
    }
}
