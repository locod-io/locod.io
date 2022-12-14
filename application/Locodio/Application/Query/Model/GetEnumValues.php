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

namespace App\Locodio\Application\Query\Model;

use App\Locodio\Domain\Model\Model\FetchType;
use App\Locodio\Domain\Model\Model\AttributeType;
use App\Locodio\Domain\Model\Model\OrderType;
use App\Locodio\Domain\Model\Model\AssociationType;
use App\Locodio\Domain\Model\Model\TemplateType;

class GetEnumValues
{
    public static function getModelEnumValues(): \stdClass
    {
        $result = new \stdClass();
        $result->attributeTypes = AttributeType::cases();
        $result->fetchTypes = FetchType::cases();
        $result->associationTypes = AssociationType::cases();
        $result->orderTypes = OrderType::cases();
        $result->templateTypes = TemplateType::cases();
        return $result;
    }
}
