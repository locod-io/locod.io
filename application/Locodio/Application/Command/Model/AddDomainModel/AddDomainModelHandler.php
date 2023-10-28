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
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\AttributeType;
use App\Locodio\Domain\Model\Model\ModuleRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddDomainModelHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected AttributeRepository   $attributeRepo,
        protected ModuleRepository      $moduleRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————————————

    public function go(AddDomainModel $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());
        $module = $this->moduleRepo->getById($command->getModuleId());

        // shift all other domain models in the sequence --------------------

        $domainModels = $this->domainModelRepo->getByProject($project);
        foreach ($domainModels as $domainModel) {
            $domainModel->setSequence($domainModel->getSequence() + 1);
            $this->domainModelRepo->save($domainModel);
        }

        // make the model --------------------------------------------------

        $model = DomainModel::make(
            $project,
            $this->domainModelRepo->nextIdentity(),
            $command->getName()
        );
        $model->setModule($module);
        $model->setArtefactId($this->domainModelRepo->getNextArtefactId($project));
        $this->domainModelRepo->save($model);

        // create two default fields: id, uuid ----------------------------

        $idField = Attribute::make(
            $model,
            $this->attributeRepo->nextIdentity(),
            'id',
            0,
            AttributeType::INTEGER,
            true,
            false,
            true,
            false,
            false,
        );
        $idField->setSequence(0);
        $idField->setArtefactId($this->attributeRepo->getNextArtefactId($model->getProject()));
        $this->attributeRepo->save($idField);

        $uuidField = Attribute::make(
            $model,
            $this->attributeRepo->nextIdentity(),
            'uuid',
            36,
            AttributeType::UUID,
            false,
            true,
            true,
            true,
            false,
        );
        $uuidField->setSequence(1);
        $uuidField->setArtefactId($idField->getArtefactId() + 1);
        $this->attributeRepo->save($uuidField);

        return true;
    }
}
