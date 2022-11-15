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

use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\EnumOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class EnumOptionRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\EnumOptionRepository
{
    public const NO_ENTITY_FOUND = 'No EnumOption found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnumOption::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function save(EnumOption $model): ?int
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

    public function getById(int $id): EnumOption
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(string $uuid): EnumOption
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

    public function delete(EnumOption $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getMaxSequence(Enum $enum): EnumOption
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.enum = :enumId')
            ->setParameter('enumId', $enum->getId())
            ->addOrderBy('t.sequence', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            $model = EnumOption::make($enum, $this->nextIdentity(), 'dummy', 'dummy');
        }
        return $model;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getByEnum(Enum $enum): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.enum = :enumId')
            ->setParameter('enumId', $enum->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
