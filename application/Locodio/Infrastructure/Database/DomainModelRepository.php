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

use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Module;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<DomainModel>
 */
final class DomainModelRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\DomainModelRepository
{
    public const NO_ENTITY_FOUND = 'No domain model found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomainModel::class);
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Implementations
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    public function getNextArtefactId(Project $project): int
    {
        $maxArtefactModel = $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $project->getId())
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

    public function save(DomainModel $model): ?int
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

    public function delete(DomainModel $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): DomainModel
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): DomainModel
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

    public function getByDocumentor(Documentor $documentor): DomainModel
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.documentor = :documentorId')
            ->setParameter('documentorId', $documentor->getId())
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    /** @return DomainModel[] */
    public function getByProject(Project $project): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $project->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countByModule(int $moduleId): int
    {
        $q = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.module = :moduleId')
            ->setParameter('moduleId', $moduleId);
        return $q->getQuery()->getSingleScalarResult();
    }

    /** @return DomainModel[] */
    public function getByModule(Module $module): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.module = :moduleId')
            ->setParameter('moduleId', $module->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
