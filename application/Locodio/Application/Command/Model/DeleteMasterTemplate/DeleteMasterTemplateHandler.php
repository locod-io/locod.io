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

namespace App\Locodio\Application\Command\Model\DeleteMasterTemplate;

use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;

class DeleteMasterTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected TemplateRepository       $templateRepo,
        protected MasterTemplateRepository $masterTemplateRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(DeleteMasterTemplate $command): bool
    {
        $masterTemplate = $this->masterTemplateRepo->getById($command->getId());
        $templates = $this->templateRepo->getByMasterTemplate($masterTemplate);
        foreach ($templates as $template) {
            $template->resetMasterTemplate();
            $this->templateRepo->save($template);
        }
        $this->masterTemplateRepo->delete($masterTemplate);

        return true;
    }
}
