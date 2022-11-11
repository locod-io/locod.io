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

namespace App\Locodio\Application\Command\Model\ImportTemplatesFromMasterTemplates;

use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class ImportTemplatesFromMasterTemplatesHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository $projectRepo,
        protected MasterTemplateRepository $masterTemplateRepo,
        protected TemplateRepository $templateRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ImportTemplatesFromMasterTemplates $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());
        $sequence = 0;
        foreach ($command->getMasterTemplateIds() as $templateId) {
            // todo avoid double imports?

            $masterTemplate = $this->masterTemplateRepo->getById($templateId);
            $template = Template::make(
                $project,
                $this->templateRepo->nextIdentity(),
                $masterTemplate->getType(),
                $masterTemplate->getName(),
                $masterTemplate->getLanguage()
            );
            $template->change(
                $masterTemplate->getType(),
                $masterTemplate->getName(),
                $masterTemplate->getLanguage(),
                $masterTemplate->getTemplate()
            );
            $template->setSequence($sequence);
            $template->importMasterTemplate($masterTemplate);
            $this->templateRepo->save($template);
            $sequence++;
        }
        return true;
    }
}
