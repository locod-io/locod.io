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

namespace App\Locodio\Application\Command\Model\ExportTemplateToMasterTemplate;

use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\User\UserRepository;

class ExportTemplateToMasterTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository           $userRepo,
        protected MasterTemplateRepository $masterTemplateRepo,
        protected TemplateRepository       $templateRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ExportTemplateToMasterTemplate $command): bool
    {
        $template = $this->templateRepo->getById($command->getTemplateId());
        $user = $this->userRepo->getById($command->getUserId());

        $masterTemplate = MasterTemplate::make(
            $user,
            $this->masterTemplateRepo->nextIdentity(),
            $template->getType(),
            $template->getName(),
            $template->getLanguage()
        );
        $masterTemplate->change(
            $template->getType(),
            $template->getName(),
            $template->getLanguage(),
            $template->getTemplate(),
            false,
            "",
            []
        );

        $masterTemplate->importTemplate($template);
        $this->masterTemplateRepo->save($masterTemplate);

        $template->importMasterTemplate($masterTemplate);
        $this->templateRepo->save($template);

        return true;
    }
}
