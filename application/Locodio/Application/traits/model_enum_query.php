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

trait model_enum_query
{
    public function getEnumValues(): \stdClass
    {
        $this->permission->CheckRole(['ROLE_USER']);

        return GetEnumValues::getModelEnumValues();
    }

    public function getEnumById(int $id): EnumRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckEnumId($id);

        $GetEnum = new GetEnum($this->enumRepo);
        return $GetEnum->ById($id);
    }
}
