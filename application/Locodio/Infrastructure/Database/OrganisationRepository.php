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

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Organisation>
 */
final class OrganisationRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Organisation\OrganisationRepository
{
    public const NO_ENTITY_FOUND = 'No organisation found.';

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organisation::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function save(Organisation $model): ?int
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

    public function getById(int $id): Organisation
    {
        $resetLink = $this->find($id);
        if (is_null($resetLink)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $resetLink;
    }

    public function getByUuid(Uuid $uuid): Organisation
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

    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // —————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    public function getByUser(User $user): array
    {
        $q = $this->createQueryBuilder('t')
            ->addOrderBy('t.sequence', 'ASC')
            ->innerJoin('t.users', 'users')
            ->where('users.id = :user_id')
            ->setParameter('user_id', $user->getId());
        return $q->getQuery()->getResult();
    }
}
