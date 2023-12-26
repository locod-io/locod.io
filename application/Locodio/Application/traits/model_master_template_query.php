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

use App\Locodio\Application\Query\Model\GetMasterTemplate;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRMCollection;
use App\Locodio\Domain\Model\User\UserRole;

trait model_master_template_query
{
    public function getMasterTemplateById(int $id): MasterTemplateRM
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);
        $this->permission->CheckMasterTemplateId($id);

        $GetMasterTemplate = new GetMasterTemplate($this->masterTemplateRepo, $this->userRepo);
        return $GetMasterTemplate->ById($id);
    }

    public function getPublicTemplates(): MasterTemplateRMCollection
    {
        $this->permission->CheckRole([UserRole::ROLE_ORGANISATION_VIEWER->value, UserRole::ROLE_ORGANISATION_USER->value]);

        $GetMasterTemplate = new GetMasterTemplate($this->masterTemplateRepo, $this->userRepo);
        return $GetMasterTemplate->Public();
    }
}
