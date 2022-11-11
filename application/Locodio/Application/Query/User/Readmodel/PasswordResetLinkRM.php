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

namespace App\Locodio\Application\Query\User\Readmodel;

use App\Locodio\Domain\Model\User\PasswordResetLink;

final class PasswordResetLinkRM
{
    // ———————————————————————————————————————————————————————————————————
    // Constructor & hydration
    // ———————————————————————————————————————————————————————————————————

    public function __construct(
        protected string    $uuid,
        protected int       $userId,
        protected bool      $isActive,
        protected bool      $isUsed,
        protected \DateTime $expiresAt,
        protected string    $code
    ) {
    }

    public static function hydrateFromModel(PasswordResetLink $model): self
    {
        return new self(
            $model->getUuid()->toRfc4122(),
            $model->getUser()->getId(),
            $model->isActive(),
            $model->isUsed(),
            $model->getExpiresAt(),
            $model->getCode()
        );
    }

    // ———————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
