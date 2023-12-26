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

use App\Locodio\Application\Query\Model\GetDomainModel;
use App\Locodio\Application\Query\Model\Readmodel\DomainModelRM;
use App\Locodio\Domain\Model\User\UserRole;

trait model_domain_model_query
{
    public function getDomainModelById(int $id): DomainModelRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckDomainModelId($id);

        $GetDomainModel = new GetDomainModel($this->domainModelRepo);
        return $GetDomainModel->ById($id);
    }
}
