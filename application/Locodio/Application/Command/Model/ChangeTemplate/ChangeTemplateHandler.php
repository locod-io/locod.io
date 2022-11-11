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

namespace App\Locodio\Application\Command\Model\ChangeTemplate;

use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateType;

class ChangeTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected TemplateRepository $templateRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ChangeTemplate $command): bool
    {
        $template = $this->templateRepo->getById($command->getId());
        $template->change(
            TemplateType::from($command->getType()),
            trim($command->getName()),
            trim($command->getLanguage()),
            trim($command->getTemplate())
        );
        $this->templateRepo->save($template);

        return true;
    }
}
