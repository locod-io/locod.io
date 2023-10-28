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
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\AttributeType;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class AttributeRepository extends ServiceEntityRepository implements \App\Locodio\Domain\Model\Model\AttributeRepository
{
    public const NO_ENTITY_FOUND = 'No field found.';

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
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
            ->join('t.domainModel', 'd')
            ->join('d.project', 'p')
            ->where('p.id = :projectId')
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

    public function save(Attribute $model): ?int
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

    public function getById(int $id): Attribute
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(string $uuid): Attribute
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

    public function getMaxSequence(DomainModel $domainModel): Attribute
    {
        $model = $this->createQueryBuilder('t')
            ->andWhere('t.domainModel = :modelId')
            ->setParameter('modelId', $domainModel->getId())
            ->addOrderBy('t.sequence', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($model)) {
            $model = Attribute::make(
                $domainModel,
                $this->nextIdentity(),
                'dummy',
                0,
                AttributeType::STRING,
                false,
                false,
                false,
                false,
                false
            );
        }
        return $model;
    }

    public function delete(Attribute $model): bool
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
