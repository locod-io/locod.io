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
use App\Lodocio\Domain\Model\Wiki\WikiNode;
use App\Lodocio\Domain\Model\Wiki\WikiNodeGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class WikiNodeRepository extends ServiceEntityRepository implements \App\Lodocio\Domain\Model\Wiki\WikiNodeRepository
{
    public const NO_ENTITY_FOUND = 'no_Wiki_node_found';

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WikiNode::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function save(WikiNode $model): ?int
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

    public function delete(WikiNode $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): WikiNode
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): WikiNode
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

    public function findByUuid(Uuid $uuid): ?WikiNode
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.uuid = :uuid')
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countByStatus(int $wikiNodeStatusId): int
    {
        $q = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.wikiNodeStatus = :statusId')
            ->setParameter('statusId', $wikiNodeStatusId);
        return $q->getQuery()->getSingleScalarResult();
    }

}