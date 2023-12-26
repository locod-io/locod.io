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

use App\Locodio\Application\Query\Model\GetQuery;
use App\Locodio\Application\Query\Model\Readmodel\QueryRM;
use App\Locodio\Domain\Model\User\UserRole;

trait model_query_query
{
    public function getQueryById(int $id): QueryRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckQueryId($id);

        $GetQuery = new GetQuery($this->queryRepo);
        return $GetQuery->ById($id);
    }
}
