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

namespace App\Locodio\Domain\Model\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait EntityId
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private Uuid $uuid;

    // ———————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getUuidAsString(): string
    {
        return $this->uuid->toRfc4122();
    }

    // ———————————————————————————————————————————————————————————————————
    // Identify
    // ———————————————————————————————————————————————————————————————————

    public function identify(int $id, Uuid $uuid): void
    {
        $this->id = $id;
        $this->uuid = $uuid;
    }
}
