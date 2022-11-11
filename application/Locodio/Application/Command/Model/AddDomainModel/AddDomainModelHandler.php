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

namespace App\Locodio\Application\Command\Model\AddDomainModel;

use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\FieldRepository;
use App\Locodio\Domain\Model\Model\FieldType;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected FieldRepository       $fieldRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————————————

    public function go(AddDomainModel $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());

        // shift all other domain models in the sequence --------------------

        $domainModels = $this->domainModelRepo->getByProject($project);
        foreach ($domainModels as $domainModel) {
            $domainModel->setSequence($domainModel->getSequence()+1);
            $this->domainModelRepo->save($domainModel);
        }

        // make the model --------------------------------------------------

        $model = DomainModel::make(
            $project,
            $this->domainModelRepo->nextIdentity(),
            $command->getName()
        );
        $this->domainModelRepo->save($model);

        // create two default fields: id, uuid ----------------------------

        $idField = Field::make(
            $model,
            $this->fieldRepo->nextIdentity(),
            'id',
            0,
            FieldType::INTEGER,
            true,
            false,
            true,
            false,
            false,
        );
        $idField->setSequence(0);
        $this->fieldRepo->save($idField);
        $uuidField = Field::make(
            $model,
            $this->fieldRepo->nextIdentity(),
            'uuid',
            36,
            FieldType::UUID,
            false,
            true,
            true,
            true,
            false,
        );
        $uuidField->setSequence(1);
        $this->fieldRepo->save($uuidField);

        return true;
    }
}
