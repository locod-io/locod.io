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

trait model_domain_model_query
{
    public function getDomainModelById(int $id): DomainModelRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckDomainModelId($id);

        $GetDomainModel = new GetDomainModel($this->domainModelRepo);
        return $GetDomainModel->ById($id);
    }
}
