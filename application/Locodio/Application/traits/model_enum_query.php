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

namespace App\Locodio\Application\traits;

use App\Locodio\Application\Query\Model\GetEnum;
use App\Locodio\Application\Query\Model\GetEnumValues;
use App\Locodio\Application\Query\Model\Readmodel\EnumRM;
use App\Locodio\Domain\Model\User\UserRole;

trait model_enum_query
{
    public function getEnumValues(): \stdClass
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);

        return GetEnumValues::getModelEnumValues();
    }

    public function getEnumById(int $id): EnumRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckEnumId($id);

        $GetEnum = new GetEnum($this->enumRepo);
        return $GetEnum->ById($id);
    }
}
