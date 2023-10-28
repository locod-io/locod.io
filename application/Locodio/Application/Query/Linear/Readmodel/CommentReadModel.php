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

namespace App\Locodio\Application\Query\Linear\Readmodel;

class CommentReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected string                      $id,
        protected string                      $body,
        protected string                      $createdAt,
        protected string                      $url,
        protected string                      $userId,
        protected string                      $userName,
        protected ?string                     $parentId,
        protected ?CommentReadModelCollection $replies = null,
    ) {
        if (is_null($this->replies)) {
            $this->replies = new CommentReadModelCollection();
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->body = $this->getBody();
        $json->createdAt = $this->getCreatedAt();
        $json->url = $this->getUrl();
        $json->userId = $this->getUserId();
        $json->userName = $this->getUserName();
        $json->parentId = $this->getParentId();
        $json->replies = $this->getReplies();

        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(array $model): self
    {
        $parentId = '';
        if (!is_null($model['parent'])) {
            $parentId = $model['parent']['id'];
        }
        return new self(
            $model['id'],
            $model['body'],
            $model['createdAt'],
            $model['url'],
            $model['user']['id'],
            $model['user']['name'],
            $parentId,
        );
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): string
    {
        return $this->id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function getReplies(): ?CommentReadModelCollection
    {
        return $this->replies;
    }
}
