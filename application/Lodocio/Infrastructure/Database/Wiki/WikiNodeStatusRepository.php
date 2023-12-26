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

namespace App\Lodocio\Infrastructure\Database\Wiki;

use App\Lodocio\Domain\Model\Wiki\Wiki;
use App\Lodocio\Domain\Model\Wiki\WikiNodeStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class WikiNodeStatusRepository extends ServiceEntityRepository implements \App\Lodocio\Domain\Model\Wiki\WikiNodeStatusRepository
{
    public const NO_ENTITY_FOUND = 'no_Wiki_node_status_found';

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WikiNodeStatus::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function save(WikiNodeStatus $model): ?int
    {
        $model->setChecksum();
        $em = $this->getEntityManager();
        $em->persist($model);
        $id = 0;
        if ($model->getId()) {
            $id = $model->getId();
        }
        return $id;
    }

    public function delete(WikiNodeStatus $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): WikiNodeStatus
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): WikiNodeStatus
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.uuid = :uuid')
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    // —————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function getAll(): array
    {
        $q = $this->createQueryBuilder('t')->andWhere('0 = 0');
        $q->addOrderBy('t.id', 'DESC');
        return $q->getQuery()->getResult();
    }

    public function getByWiki(Wiki $wiki): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.wiki = :wikiId')
            ->setParameter('wikiId', $wiki->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }

    public function getNextArtefactId(Wiki $wiki): int
    {
        $maxArtefactModel = $this->createQueryBuilder('t')
            ->andWhere('t.wiki = :wikiId')
            ->setParameter('wikiId', $wiki->getId())
            ->addOrderBy('t.artefactId', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $result = 0;
        if (!is_null($maxArtefactModel)) {
            $result = $maxArtefactModel->getArtefactId();
        }
        return $result + 1;
    }

    public function getMaxSequence(Wiki $wiki): int
    {
        $maxSequenceModel = $this->createQueryBuilder('t')
            ->andWhere('t.wiki = :wikiId')
            ->setParameter('wikiId', $wiki->getId())
            ->addOrderBy('t.sequence', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $result = 0;
        if (!is_null($maxSequenceModel)) {
            $result = $maxSequenceModel->getSequence();
        }
        return $result + 1;
    }

    public function findFirstStatus(Wiki $wiki): ?WikiNodeStatus
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.wiki = :wikiId')
            ->setParameter('wikiId', $wiki->getId())
            ->andWhere('t.isStart = true')
            ->addOrderBy('t.sequence', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFinalStatus(Wiki $wiki): ?WikiNodeStatus
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.wiki = :wikiId')
            ->setParameter('wikiId', $wiki->getId())
            ->andWhere('t.isFinal = true')
            ->addOrderBy('t.sequence', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
