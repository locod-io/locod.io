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

namespace App\Locodio\Application\Command\Model\AddAttribute;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\Attribute;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\AttributeType;

class AddAttributeHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected AttributeRepository   $attributeRepo,
        protected EnumRepository        $enumRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddAttribute $command): bool
    {
        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        if (ModelFinalChecker::isFinalState($domainModel->getDocumentor())) {
            return false;
        }

        $model = Attribute::make(
            $domainModel,
            $this->attributeRepo->nextIdentity(),
            $command->getName(),
            $command->getLength(),
            AttributeType::from($command->getType()),
            $command->isIdentifier(),
            $command->isRequired(),
            $command->isUnique(),
            $command->isMake(),
            $command->isChange(),
        );

        if ($command->getEnumId() === 0) {
            $model->setEnum(null);
        } else {
            $enum = $this->enumRepo->getById($command->getEnumId());
            $model->setEnum($enum);
        }

        $lastSequence = $this->attributeRepo->getMaxSequence($domainModel)->getSequence() + 1;
        $model->setSequence($lastSequence);
        $model->setArtefactId($this->attributeRepo->getNextArtefactId($domainModel->getProject()));
        $this->attributeRepo->save($model);

        return true;
    }
}
