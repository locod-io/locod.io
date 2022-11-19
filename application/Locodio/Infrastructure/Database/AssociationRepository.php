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

namespace App\Locodio\Infrastructure\Database;

use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Association;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class AssociationRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\AssociationRepository
{
    public const NO_ENTITY_FOUND = 'No Relation found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Association::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function save(Association $model): ?int
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

    public function getById(int $id): Association
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(string $uuid): Association
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function delete(Association $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getByDomainModel(DomainModel $domainModel): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.domainModel = :domainModelId')
            ->setParameter('domainModelId', $domainModel->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
