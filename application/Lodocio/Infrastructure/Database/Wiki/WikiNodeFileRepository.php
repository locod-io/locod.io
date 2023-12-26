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
use App\Lodocio\Domain\Model\Wiki\WikiNodeFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class WikiNodeFileRepository extends ServiceEntityRepository implements \App\Lodocio\Domain\Model\Wiki\WikiNodeFileRepository
{
    public const NO_ENTITY_FOUND = 'no_Wiki_node_file_found';

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WikiNodeFile::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function save(WikiNodeFile $model): ?int
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

    public function delete(WikiNodeFile $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): WikiNodeFile
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): WikiNodeFile
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

    public function findByWikiNodeAndName(WikiNode $wikiNode, string $name): ?WikiNodeFile
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :name')
            ->setParameter('name', $name)
            ->andWhere('t.wikiNode = :wikiNodeId')
            ->setParameter('wikiNodeId', $wikiNode->getId())
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

    public function getByWikiNode(WikiNode $wikiNode): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.wikiNode = :wikiNodeId')
            ->setParameter('wikiNodeId', $wikiNode->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }

    public function getNextArtefactId(Wiki $wiki): int
    {
        $maxArtefactModel = $this->createQueryBuilder('tf')
            ->join('tf.wikiNode', 'tn')
            ->join('tn.wiki', 't')
            ->andWhere('t.id = :wikiId')
            ->setParameter('wikiId', $wiki->getId())
            ->addOrderBy('tf.artefactId', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $result = 0;
        if (!is_null($maxArtefactModel)) {
            $result = $maxArtefactModel->getArtefactId();
        }
        return $result + 1;
    }

}
