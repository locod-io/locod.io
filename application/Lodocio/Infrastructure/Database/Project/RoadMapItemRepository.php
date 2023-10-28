<?php

declare(strict_types=1);

namespace App\Lodocio\Infrastructure\Database\Project;

use App\Lodocio\Domain\Model\Project\RoadMap;
use App\Lodocio\Domain\Model\Project\RoadMapItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class RoadMapItemRepository extends ServiceEntityRepository implements \App\Lodocio\Domain\Model\Project\RoadMapItemRepository
{
    public const NO_ENTITY_FOUND = 'no_road_map_item_found';

    // —————————————————————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————————————————————

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoadMapItem::class);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::v4();
    }

    // —————————————————————————————————————————————————————————————————————————
    // Single entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function save(RoadMapItem $model): ?int
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

    public function delete(RoadMapItem $model): bool
    {
        $em = $this->getEntityManager();
        $em->remove($model);
        return true;
    }

    public function getById(int $id): RoadMapItem
    {
        $model = $this->find($id);
        if (is_null($model)) {
            throw new EntityNotFoundException(self::NO_ENTITY_FOUND);
        }
        return $model;
    }

    public function getByUuid(Uuid $uuid): RoadMapItem
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

    // —————————————————————————————————————————————————————————————————————————
    // Multiple entity functions
    // —————————————————————————————————————————————————————————————————————————

    public function getByRoadMap(RoadMap $roadMap): array
    {
        $q = $this->createQueryBuilder('t')->andWhere('0 = 0')
            ->andWhere('t.roadMap= :roadmapId')
            ->setParameter('roadmapId', $roadMap->getId())
            ->addOrderBy('t.sequence', 'ASC');
        return $q->getQuery()->getResult();
    }

}
