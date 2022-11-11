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

use App\Locodio\Domain\Model\User\PasswordResetLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<PasswordResetLink>
 */
final class PasswordResetLinkRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\User\PasswordResetLinkRepository
{
    public const NO_ENTITY_FOUND = 'No Link found.';

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetLink::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function save(PasswordResetLink $model): ?int
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

    public function getById(int $id): PasswordResetLink
    {
        $resetLink = $this->find($id);
        if (is_null($resetLink)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $resetLink;
    }

    public function getByUuid(Uuid $uuid): PasswordResetLink
    {
        $resetLink = $this->createQueryBuilder('t')
            ->andWhere('t.uuid = :uuid')
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($resetLink)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $resetLink;
    }

    public function getByCode(string $code): PasswordResetLink
    {
        $resetLink = $this->createQueryBuilder('t')
            ->andWhere('t.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($resetLink)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $resetLink;
    }

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    /**
     * @param int $userId
     * @return PasswordResetLink[]
     */
    public function getByUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
