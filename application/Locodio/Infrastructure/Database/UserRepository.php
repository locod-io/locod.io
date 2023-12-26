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
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, \App\Locodio\Domain\Model\User\UserRepository
{
    public const NO_ENTITY_FOUND = 'No User found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Save & remove
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function save(User $entity, bool $flush = false): ?int
    {
        $entity->setChecksum();
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        $id = 0;
        if ($entity->getId()) {
            $id = $entity->getId();
        }

        return $id;
    }

    //    public function remove(User $entity, bool $flush = false): void
    //    {
    //        $this->getEntityManager()->remove($entity);
    //
    //        if ($flush) {
    //            $this->getEntityManager()->flush();
    //        }
    //    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Single Entity Queries
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getById(int $id): User
    {
        $user = $this->find($id);
        if (is_null($user)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $user;
    }

    public function findById(int $id): ?User
    {
        $user = $this->find($id);
        return $user;
    }

    public function getByEmail(string $email): User
    {
        $user = $this->createQueryBuilder('t')
            ->andWhere('t.email = :email')
            ->setParameter('email', trim(strtolower($email)))
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($user)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.email = :email')
            ->setParameter('email', trim(strtolower($email)))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByUuid(Uuid $uuid): User
    {
        $user = $this->createQueryBuilder('t')
            ->andWhere('t.uuid = :uuid')
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($user)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $user;
    }

    public function getByOrganisation(Organisation $organisation): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.organisations', 'o')
            ->where('o.id = :organisationId')
            ->setParameter('organisationId', $organisation->getId())
            ->getQuery()
            ->getResult();
    }

}
