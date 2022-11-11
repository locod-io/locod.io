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

namespace App\Locodio\Application\Command\Model\AddTemplate;

use App\Locodio\Domain\Model\Model\Template;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class AddTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository  $projectRepo,
        protected TemplateRepository $templateRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddTemplate $command): bool
    {
        $project = $this->projectRepo->getById($command->getProjectId());

        $templates = $this->templateRepo->getByProject($project);
        foreach ($templates as $template) {
            $template->setSequence($template->getSequence()+1);
            $this->templateRepo->save($template);
        }



        $template = Template::make(
            $project,
            $this->templateRepo->nextIdentity(),
            TemplateType::from($command->getType()),
            $command->getName(),
            $command->getLanguage()
        );
        $this->templateRepo->save($template);

        return true;
    }
}
