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

namespace App\Locodio\Application\Command\Model\AddRelation;

use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Relation;
use App\Locodio\Domain\Model\Model\RelationRepository;
use App\Locodio\Domain\Model\Model\RelationType;

class AddRelationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected RelationRepository    $relationRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddRelation $command): bool
    {
        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $targetDomainModel = $this->domainModelRepo->getById($command->getTargetDomainModelId());

        $mappedBy = '';
        $inversedBy = '';
        $reverseMappedBy = '';
        $reverseInversedBy = '';

        // -- determine mapping & inversion

        $relationType = RelationType::from($command->getType());
        switch ($relationType) {
            case RelationType::OTOB: // 'One-To-One_Bidirectional';
                $mappedBy = lcfirst($domainModel->getName());
                $reverseInversedBy = lcfirst($targetDomainModel->getName());
                break;
            case RelationType::MTMB: // 'Many-To-Many_Bidirectional';
                $mappedBy = lcfirst($domainModel->getName()) . 's';
                $reverseInversedBy = lcfirst($targetDomainModel->getName()) . 's';
                break;
            case RelationType::OTMB: // 'One-To-Many_Bidirectional';
                $mappedBy = lcfirst($domainModel->getName());
                $reverseInversedBy = lcfirst($targetDomainModel->getName()) . 's';
                $reverseMappedBy = lcfirst($domainModel->getName());
                break;
            case RelationType::MTOU: // 'Many-To-One_Unidirectional';
                $inversedBy = lcfirst($domainModel->getName()) . 's';
                $mappedBy = lcfirst($targetDomainModel->getName());
                break;
            case RelationType::OTOU: //'One-To-One_Unidirectional';
                $mappedBy = lcfirst($targetDomainModel->getName());
                break;
            case RelationType::MTMU: // 'Many-To-Many_Unidirectional';
                $mappedBy = lcfirst($targetDomainModel->getName()) . 's';
                break;
            case RelationType::OTMS: // 'One-To-Many_Self-referencing';
                $mappedBy = 'parent';
                $inversedBy = 'children';
                break;
            case RelationType::OTOS: // 'One-To-One_Self-referencing';
                $mappedBy = 'someVariable';
                break;
            case RelationType::MTMS: // 'Many-To-Many_Self-referencing';
                $mappedBy = 'myRelations';
                $inversedBy = 'relationsWithMe';
                break;
        }

        $model = Relation::make(
            $domainModel,
            $this->relationRepo->nextIdentity(),
            $relationType,
            $mappedBy,
            $inversedBy,
            FetchType::from($command->getFetch()),
            $command->getOrderBy(),
            OrderType::ASC,
            $targetDomainModel
        );

        // -- create the reversed link

        if ($relationType == RelationType::OTOB
            || $relationType == RelationType::MTMB
            || $relationType == RelationType::OTMB
        ) {
            $reversedType = $relationType;
            if ($relationType == RelationType::OTMB) {
                $reversedType = RelationType::MTOU;
            }
            if ($relationType == RelationType::MTOU) {
                $reversedType = RelationType::OTMB;
            }
            $reversedRelation = Relation::make(
                $targetDomainModel,
                $this->relationRepo->nextIdentity(),
                $reversedType,
                $reverseMappedBy,
                $reverseInversedBy,
                FetchType::from($command->getFetch()),
                $command->getOrderBy(),
                OrderType::ASC,
                $domainModel,
            );
            $this->relationRepo->save($reversedRelation);
        }

        $this->relationRepo->save($model);

        return true;
    }
}
