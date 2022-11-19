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

use App\Locodio\Application\Query\Model\GetTemplate;
use App\Locodio\Application\Query\Model\Readmodel\GeneratedCodeRM;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;

trait model_template_query
{
    public function getTemplateById(int $id): TemplateRM
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($id);

        $GetTemplate = new GetTemplate(
            $this->templateRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo
        );
        return $GetTemplate->ById($id);
    }

    public function generateTemplateBySubjectId(
        int $id,
        int $subjectId
    ): GeneratedCodeRM {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTemplateId($id);

        $GetTemplate = new GetTemplate(
            $this->templateRepo,
            $this->domainModelRepo,
            $this->enumRepo,
            $this->queryRepo,
            $this->commandRepo
        );
        return $GetTemplate->GenerateBySubjectId($id, $subjectId);
    }
}
