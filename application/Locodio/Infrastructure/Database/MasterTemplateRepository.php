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

use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class MasterTemplateRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\MasterTemplateRepository
{
    public const NO_ENTITY_FOUND = 'No Command found.';

    // —————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MasterTemplate::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————

    public function save(MasterTemplate $model, bool $flush = false): ?int
    {
        $model->setChecksum();
        $em = $this->getEntityManager();
        $em->persist($model);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        $id = 0;
        if ($model->getId()) {
            $id = $model->getId();
        }

        return $id;
    }

    public function delete(MasterTemplate $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): MasterTemplate
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(string $uuid): MasterTemplate
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

    // —————————————————————————————————————————————————————————————
    // Multiple entity functions
    // —————————————————————————————————————————————————————————————

    /** @return MasterTemplate[] */
    public function getByUser(User $user): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.user = :userId')
            ->setParameter('userId', $user->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }

    /** @return MasterTemplate[] */
    public function getPublicTemplates(): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->addOrderBy('t.updatedAt', 'DESC');
        return $q->getQuery()->getResult();
    }
}
