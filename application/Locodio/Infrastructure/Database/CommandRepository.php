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

use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class CommandRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\CommandRepository
{
    public const NO_ENTITY_FOUND = 'No Command found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

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

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function save(Command $model): ?int
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

    public function delete(Command $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): Command
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(string $uuid): Command
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

    public function getByDocumentor(Documentor $documentor): Command
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

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    /** @return Command[] */
    public function getByProject(Project $project): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $project->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }

    /** @return Command[] */
    public function getByDomainModel(DomainModel $domainModel): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.domainModel = :domainModelId')
            ->setParameter('domainModelId', $domainModel->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
