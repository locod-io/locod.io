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

namespace App\Locodio\Application\Command\Model\ChangeAttribute;

use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\AttributeRepository;
use App\Locodio\Domain\Model\Model\AttributeType;

use Doctrine\ORM\EntityNotFoundException;

class ChangeAttributeHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected AttributeRepository $attributeRepo,
        protected EnumRepository      $enumRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    /**
     * @throws EntityNotFoundException
     */
    public function go(ChangeAttribute $command): bool
    {
        $model = $this->attributeRepo->getById($command->getId());
        $model->change(
            $command->getName(),
            $command->getLength(),
            AttributeType::from($command->getType()),
            $command->isIdentifier(),
            $command->isRequired(),
            $command->isUnique(),
            $command->isMake(),
            $command->isChange(),
        );

        if ($command->getEnumId() === 0 || AttributeType::from($command->getType()) !== AttributeType::ENUM) {
            $model->setEnum(null);
        } else {
            $enum = $this->enumRepo->getById($command->getEnumId());
            $model->setEnum($enum);
        }

        $this->attributeRepo->save($model);

        return true;
    }
}
