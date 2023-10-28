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

class IssueReadModel implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected string                      $id,
        protected string                      $identifier,
        protected string                      $title,
        protected string                      $url,
        protected string                      $description,
        protected string                      $assigneeId,
        protected string                      $assigneeName,
        protected string                      $createdAt,
        protected string                      $archivedAt,
        protected string                      $completedAt,
        protected StateReadModel              $state,
        protected ?IssueReadModelCollection   $children = null,
        protected ?CommentReadModelCollection $comments = null,
    ) {
    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->id = $this->getId();
        $json->identifier = $this->getIdentifier();
        $json->title = $this->getTitle();
        $json->url = $this->getUrl();
        $json->description = $this->getDescription();
        $json->assigneeId = $this->getAssigneeId();
        $json->assigneeName = $this->getAssigneeName();
        $json->createdAt = $this->getCreatedAt();
        $json->completedAt = $this->getCompletedAt();
        $json->archivedAt = $this->getArchivedAt();
        $json->state = $this->getState();
        if (!is_null($this->getChildren())) {
            $json->subIssues = $this->getChildren();
        }
        if (!is_null($this->getComments())) {
            $json->comments = $this->getComments();
        }
        return $json;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Hydrate from model
    // —————————————————————————————————————————————————————————————————————————

    public static function hydrateFromModel(array $model, bool $full = false): self
    {
        $assigneeId = '';
        $assigneeName = '';
        if (!is_null($model['assignee'])) {
            $assigneeId = $model['assignee']['id'];
            $assigneeName = $model['assignee']['name'];
        }
        $description = '';
        if (isset($model['description'])) {
            $description = $model['description'];
        }
        $archivedAt = '';
        if (!is_null($model['archivedAt'])) {
            $archivedAt = $model['archivedAt'];
        }
        $completedAt = '';
        if (!is_null($model['completedAt'])) {
            $completedAt = $model['completedAt'];
        }

        if ($full) {
            $subIssues = new IssueReadModelCollection();
            foreach ($model['children']['nodes'] as $issue) {
                $subIssues->addItem(IssueReadModel::hydrateFromModel($issue, false));
            }
            $comments = new CommentReadModelCollection();
            foreach ($model['comments']['nodes'] as $comment) {
                $comments->addItem(CommentReadModel::hydrateFromModel($comment));
            }
            $comments->makeReplyTree();

            return new self(
                trim($model['id']),
                trim($model['identifier']),
                trim($model['title']),
                trim($model['url']),
                trim($description),
                trim($assigneeId),
                trim($assigneeName),
                trim($model['createdAt']),
                trim($archivedAt),
                trim($completedAt),
                StateReadModel::hydrateFromModel($model['state']),
                $subIssues,
                $comments
            );
        } else {
            return new self(
                trim($model['id']),
                trim($model['identifier']),
                trim($model['title']),
                trim($model['url']),
                trim($description),
                trim($assigneeId),
                trim($assigneeName),
                trim($model['createdAt']),
                trim($archivedAt),
                trim($completedAt),
                StateReadModel::hydrateFromModel($model['state']),
            );
        }
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAssigneeId(): string
    {
        return $this->assigneeId;
    }

    public function getAssigneeName(): string
    {
        return $this->assigneeName;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getArchivedAt(): string
    {
        return $this->archivedAt;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getCompletedAt(): string
    {
        return $this->completedAt;
    }

    public function getState(): StateReadModel
    {
        return $this->state;
    }

    public function getChildren(): ?IssueReadModelCollection
    {
        return $this->children;
    }

    public function getComments(): ?CommentReadModelCollection
    {
        return $this->comments;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

}
