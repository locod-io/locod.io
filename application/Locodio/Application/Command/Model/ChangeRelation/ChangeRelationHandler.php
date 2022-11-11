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

namespace App\Locodio\Application\Command\Model\ChangeRelation;

use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\RelationRepository;
use App\Locodio\Domain\Model\Model\RelationType;

class ChangeRelationHandler
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

    public function go(ChangeRelation $command): bool
    {
        $model = $this->relationRepo->getById($command->getId());
        $targetDomainModel = $this->domainModelRepo->getById($command->getTargetDomainModelId());
        $model->change(
            RelationType::from($command->getType()),
            $command->getMappedBy(),
            $command->getInversedBy(),
            FetchType::from($command->getFetch()),
            $command->getOrderBy(),
            OrderType::from($command->getOrderDirection()),
            $targetDomainModel,
            $command->isMake(),
            $command->isChange(),
            $command->isRequired()
        );

        $this->relationRepo->save($model);

        return true;
    }
}
