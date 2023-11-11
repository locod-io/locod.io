<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Infrastructure\Database\Project;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\Project;
use App\Lodocio\Domain\Model\Project\DocProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<DocProject>
 */
final class DocProjectRepository extends ServiceEntityRepository implements \App\Lodocio\Domain\Model\Project\DocProjectRepository
{
    public const NO_ENTITY_FOUND = 'No doc project found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocProject::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function save(DocProject $model): ?int
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

    public function getById(int $id): DocProject
    {
        $resetLink = $this->find($id);
        if (is_null($resetLink)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $resetLink;
    }

    public function getByUuid(Uuid $uuid): DocProject
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

    public function findByProject(Project $project): ?DocProject
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.project = :projectId')
            ->setParameter('projectId', $project->getId())
            ->getQuery()
            ->getOneOrNullResult();
        return $model;
    }

    public function getByProject(Project $project): DocProject
    {
        $model = $this->findByProject($project);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // ———————————————————————————————————————————————————————————————————————————————————————

    /** @return DocProject[] */
    public function getByOrganisation(Organisation $organisation): array
    {
        $q = $this->createQueryBuilder('t')
            ->andWhere('t.organisation = :organisationId')
            ->setParameter('organisationId', $organisation->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }
}
