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

namespace App\Locodio\Application\Command\Model\ChangeMasterTemplate;

use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateType;

class ChangeMasterTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected MasterTemplateRepository $masterTemplateRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeMasterTemplate $command): bool
    {
        $masterTemplate = $this->masterTemplateRepo->getById($command->getId());
        $masterTemplate->change(
            TemplateType::from($command->getType()),
            trim($command->getName()),
            trim($command->getLanguage()),
            trim($command->getTemplate()),
            $command->isPublic(),
            trim($command->getDescription()),
            $command->getTags()
        );
        $this->masterTemplateRepo->save($masterTemplate);

        return true;
    }
}
