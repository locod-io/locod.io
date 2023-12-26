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
use App\Locodio\Domain\Model\User\UserInvitationLink;
use App\Locodio\Domain\Model\User\UserRegistrationLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class UserInvitationLinkRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\User\UserInvitationLinkRepository
{
    public const NO_ENTITY_FOUND = 'No User Invitation Link found.';

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInvitationLink::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function save(UserInvitationLink $model): ?int
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

    public function delete(UserInvitationLink $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): UserInvitationLink
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): UserInvitationLink
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

    public function getByCode(string $code): UserInvitationLink
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function findByOrganisationAndEmail(Organisation $organisation, string $email): ?UserInvitationLink
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.email = :email')
            ->setParameter('email', $email)
            ->andWhere('t.organisation = :organisationId')
            ->setParameter('organisationId', $organisation->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getActiveByOrganisation(Organisation $organisation): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.organisation = :organisationId')
            ->setParameter('organisationId', $organisation->getId())
            ->andWhere('t.isUsed = false')
            ->getQuery()
            ->getResult();
    }

}
