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

namespace App\Locodio\Application\Command\Model\ChangeAssociation;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\AssociationRepository;
use App\Locodio\Domain\Model\Model\AssociationType;

class ChangeAssociationHandler
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

    public function go(ChangeAssociation $command): bool
    {
        $model = $this->associationRepo->getById($command->getId());

        if (ModelFinalChecker::isFinalState($model->getDomainModel()->getDocumentor())) {
            return false;
        }

        $targetDomainModel = $this->domainModelRepo->getById($command->getTargetDomainModelId());
        $model->change(
            AssociationType::from($command->getType()),
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

        $this->associationRepo->save($model);

        return true;
    }
}
