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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class ModuleRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\ModuleRepository
{
    public const NO_ENTITY_FOUND = 'no_module_found';

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function save(Module $model): ?int
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

    public function delete(Module $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): Module
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): Module
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

    public function getMaxSequence(Project $project): Module
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $project->getId())
            ->addOrderBy('t.sequence', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            $model = Module::make(
                $project,
                $this->nextIdentity(),
                'name',
                'namespace',
            );
        }
        return $model;
    }

    public function getByDocumentor(Documentor $documentor): Module
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

    /** @return Module[] */
    public function getByProject(Project $project): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $project->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
