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
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\FieldType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class FieldRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\FieldRepository
{
    public const NO_ENTITY_FOUND = 'No field found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Field::class);
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Implementations
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    public function save(Field $model): ?int
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

    public function getById(int $id): Field
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(string $uuid): Field
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

    public function getMaxSequence(DomainModel $domainModel): Field
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.domainModel = :modelId')
            ->setParameter('modelId', $domainModel->getId())
            ->addOrderBy('t.sequence', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            $model = Field::make(
                $domainModel,
                $this->nextIdentity(),
                'dummy',
                0,
                FieldType::STRING,
                false,
                false,
                false,
                false,
                false
            );
        }
        return $model;
    }

    public function delete(Field $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getByDomainModel(DomainModel $domainModel): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.domainModel = :domainModelId')
            ->setParameter('domainModelId', $domainModel->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
