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

namespace App\Locodio\Application\Command\Model\ChangeField;

use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\FieldRepository;
use App\Locodio\Domain\Model\Model\FieldType;

use Doctrine\ORM\EntityNotFoundException;

class ChangeFieldHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected FieldRepository $fieldRepo,
        protected EnumRepository  $enumRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    /**
     * @throws EntityNotFoundException
     */
    public function go(ChangeField $command): bool
    {
        $model = $this->fieldRepo->getById($command->getId());
        $model->change(
            $command->getName(),
            $command->getLength(),
            FieldType::from($command->getType()),
            $command->isIdentifier(),
            $command->isRequired(),
            $command->isUnique(),
            $command->isMake(),
            $command->isChange(),
        );

        if ($command->getEnumId() === 0 || FieldType::from($command->getType()) !== FieldType::ENUM) {
            $model->setEnum(null);
        } else {
            $enum = $this->enumRepo->getById($command->getEnumId());
            $model->setEnum($enum);
        }

        $this->fieldRepo->save($model);

        return true;
    }
}
