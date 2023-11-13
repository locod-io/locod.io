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

namespace App\Linear\Application\Query\Readmodel;

class CommentReadModelCollection implements \JsonSerializable
{
    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(protected array $collection = [])
    {

    }

    // —————————————————————————————————————————————————————————————————————————
    // What to render as json
    // —————————————————————————————————————————————————————————————————————————

    public function jsonSerialize(): \stdClass
    {
        $json = new \stdClass();
        $json->collection = $this->getCollection();
        return $json;
    }

    public function addItem(CommentReadModel $readModel): void
    {
        $this->collection[] = $readModel;
    }

    public function makeReplyTree(): void
    {
        /** @var CommentReadModel $comment */
        foreach ($this->collection as $comment) {
            if (strlen($comment->getParentId()) === 0) {
                /** @var CommentReadModel $reply */
                foreach ($this->findRepliesFor($comment->getId())->getCollection() as $reply) {
                    $comment->getReplies()->addItem($reply);
                }
            }
        }
        $filteredComments = [];
        foreach ($this->collection as $comment) {
            if (strlen($comment->getParentId()) === 0) {
                $filteredComments[] = $comment;
            }
        }
        $this->collection = array_reverse($filteredComments);
    }

    private function findRepliesFor(string $id): CommentReadModelCollection
    {
        $replyCollection = new self();
        /** @var CommentReadModel $comment */
        foreach ($this->collection as $comment) {
            if ($comment->getParentId() === $id) {
                $replyCollection->addItem($comment);
            }
        }
        $replyCollection->reverseCollection();
        return $replyCollection;
    }

    public function reverseCollection(): void
    {
        $this->collection = array_reverse($this->collection);
    }

    // —————————————————————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————————————————————

    /**
     * @return CommentReadModel[]
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

}
