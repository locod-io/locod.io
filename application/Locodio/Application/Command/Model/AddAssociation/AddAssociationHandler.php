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

namespace App\Locodio\Application\Command\Model\AddAssociation;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\Association;
use App\Locodio\Domain\Model\Model\AssociationRepository;
use App\Locodio\Domain\Model\Model\AssociationType;

class AddAssociationHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected AssociationRepository $associationRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddAssociation $command): bool
    {
        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        if (ModelFinalChecker::isFinalState($domainModel->getDocumentor())) {
            return false;
        }

        $targetDomainModel = $this->domainModelRepo->getById($command->getTargetDomainModelId());
        $mappedBy = '';
        $inversedBy = '';
        $reverseMappedBy = '';
        $reverseInversedBy = '';

        // -- determine mapping & inversion

        $relationType = AssociationType::from($command->getType());
        switch ($relationType) {
            case AssociationType::OTOB: // 'One-To-One_Bidirectional';
                $mappedBy = lcfirst($domainModel->getName());
                $reverseInversedBy = lcfirst($targetDomainModel->getName());
                break;
            case AssociationType::MTMB: // 'Many-To-Many_Bidirectional';
                $mappedBy = lcfirst($domainModel->getName()) . 's';
                $reverseInversedBy = lcfirst($targetDomainModel->getName()) . 's';
                break;
            case AssociationType::OTMB: // 'One-To-Many_Bidirectional';
                $mappedBy = lcfirst($domainModel->getName());
                $reverseInversedBy = lcfirst($targetDomainModel->getName()) . 's';
                $reverseMappedBy = lcfirst($domainModel->getName());
                break;
            case AssociationType::MTOU: // 'Many-To-One_Unidirectional';
                $inversedBy = lcfirst($domainModel->getName()) . 's';
                $mappedBy = lcfirst($targetDomainModel->getName());
                break;
            case AssociationType::OTOU: //'One-To-One_Unidirectional';
                $mappedBy = lcfirst($targetDomainModel->getName());
                break;
            case AssociationType::MTMU: // 'Many-To-Many_Unidirectional';
                $mappedBy = lcfirst($targetDomainModel->getName()) . 's';
                break;
            case AssociationType::OTMS: // 'One-To-Many_Self-referencing';
                $mappedBy = 'parent';
                $inversedBy = 'children';
                break;
            case AssociationType::OTOS: // 'One-To-One_Self-referencing';
                $mappedBy = 'someVariable';
                break;
            case AssociationType::MTMS: // 'Many-To-Many_Self-referencing';
                $mappedBy = 'myRelations';
                $inversedBy = 'relationsWithMe';
                break;
        }

        $model = Association::make(
            $domainModel,
            $this->associationRepo->nextIdentity(),
            $relationType,
            $mappedBy,
            $inversedBy,
            FetchType::from($command->getFetch()),
            $command->getOrderBy(),
            OrderType::ASC,
            $targetDomainModel
        );

        // -- create the reversed link

        if ($relationType == AssociationType::OTOB
            || $relationType == AssociationType::MTMB
            || $relationType == AssociationType::OTMB
        ) {
            $reversedType = $relationType;
            if ($relationType == AssociationType::OTMB) {
                $reversedType = AssociationType::MTOU;
            }
            if ($relationType == AssociationType::MTOU) {
                $reversedType = AssociationType::OTMB;
            }
            $reversedRelation = Association::make(
                $targetDomainModel,
                $this->associationRepo->nextIdentity(),
                $reversedType,
                $reverseMappedBy,
                $reverseInversedBy,
                FetchType::from($command->getFetch()),
                $command->getOrderBy(),
                OrderType::ASC,
                $domainModel,
            );
            $this->associationRepo->save($reversedRelation);
        }

        $this->associationRepo->save($model);

        return true;
    }
}
