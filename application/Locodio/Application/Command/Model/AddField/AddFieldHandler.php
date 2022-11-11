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

namespace App\Locodio\Application\Command\Model\AddField;

use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\Field;
use App\Locodio\Domain\Model\Model\FieldRepository;
use App\Locodio\Domain\Model\Model\FieldType;

use Doctrine\ORM\EntityNotFoundException;

class AddFieldHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected DomainModelRepository $domainModelRepo,
        protected FieldRepository       $fieldRepo,
        protected EnumRepository        $enumRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    /**
     * @throws EntityNotFoundException
     */
    public function go(AddField $command): bool
    {
        $domainModel = $this->domainModelRepo->getById($command->getDomainModelId());
        $model = Field::make(
            $domainModel,
            $this->fieldRepo->nextIdentity(),
            $command->getName(),
            $command->getLength(),
            FieldType::from($command->getType()),
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

        $lastSequence = $this->fieldRepo->getMaxSequence($domainModel)->getSequence();
        $model->setSequence($lastSequence++);

        $this->fieldRepo->save($model);

        return true;
    }
}
