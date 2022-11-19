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

namespace App\Locodio\Application\Command\Model\ChangeMasterTemplateContent;

use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;

class ChangeMasterTemplateContentHandler
{
    public function __construct(
        protected TemplateRepository       $templateRepo,
        protected MasterTemplateRepository $masterTemplateRepo
    ) {
    }

    public function go(ChangeMasterTemplateContent $command): bool
    {
        $template = $this->templateRepo->getById($command->getTemplateId());
        $masterTemplate = $this->masterTemplateRepo->getById($command->getMasterTemplateId());
        // -- see if both templates are connected
        if ($template->getMasterTemplate()->getId() === $masterTemplate->getId()) {
            $masterTemplate->changeTemplateContents($template->getTemplate());
            $this->masterTemplateRepo->save($masterTemplate);
            return true;
        }
        return false;
    }
}
