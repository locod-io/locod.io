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

namespace App\Locodio\Application\Command\Model\ChangeTemplateContent;

use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;

class ChangeTemplateContentHandler
{
    public function __construct(
        protected TemplateRepository       $templateRepo,
        protected MasterTemplateRepository $masterTemplateRepo
    ) {
    }

    public function go(ChangeTemplateContent $command): bool
    {
        $template = $this->templateRepo->getById($command->getTemplateId());
        $masterTemplate = $this->masterTemplateRepo->getById($command->getMasterTemplateId());
        // -- see if both templates are connected
        if ($template->getMasterTemplate()->getId() === $masterTemplate->getId()) {
            $template->changeTemplateContents($masterTemplate->getTemplate());
            // -- re-import the master template for resetting the date
            $template->importMasterTemplate($masterTemplate);
            $this->templateRepo->save($template);
        }
        return true;
    }
}
