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

namespace App\Lodocio\Application\Query\Audit\Readmodel;

use DH\Auditor\Model\Entry;

class AuditTrailItem implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected AuditTrailItemType    $type,
        protected int                   $userId,
        protected \DateTimeImmutable    $createdAt,
        protected int                   $createdAtNumber,
        protected AuditTrailItemSubject $subject = AuditTrailItemSubject::TRACKER,
        protected string                $initials = '',
        protected string                $color = '',
        protected string                $code = '',
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->type = $this->getType()->value;
        $json->userId = $this->getUserId();
        $json->createdAt = $this->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $json->createdAtNumber = $this->getCreatedAtNumber();
        $json->subject = $this->getSubject()->value;
        $json->initials = $this->getInitials();
        $json->color = $this->getColor();
        $json->code = $this->getCode();
        return $json;
    }

    public static function hydrateFromModel(Entry $model): self
    {
        $type = AuditTrailItemType::CREATED;
        switch ($model->getType()) {
            case 'update':
                $type = AuditTrailItemType::CHANGED;
                break;
            case 'delete':
                $type = AuditTrailItemType::DELETED;
                break;
        }
        $userId = 0;
        if (!is_null($model->getUserId())) {
            $userId = (int)$model->getUserId();
        }
        $createdAt = new \DateTimeImmutable($model->getCreatedAt());
        $createdAtNumber = (int)$createdAt->format('YmdHis');
        return new self($type, $userId, $createdAt, $createdAtNumber);
    }

    // —————————————————————————————————————————————————————————————————————————
    // Setters
    // —————————————————————————————————————————————————————————————————————————

    public function setSubject(AuditTrailItemSubject $subject): void
    {
        $this->subject = $subject;
    }

    public function setInitialsAndColor(string $initials, string $color): void
    {
        $this->initials = $initials;
        $this->color = $color;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getType(): AuditTrailItemType
    {
        return $this->type;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAtNumber(): int
    {
        return $this->createdAtNumber;
    }

    public function getSubject(): AuditTrailItemSubject
    {
        return $this->subject;
    }

    public function getInitials(): string
    {
        return $this->initials;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getCode(): string
    {
        return $this->code;
    }

}
