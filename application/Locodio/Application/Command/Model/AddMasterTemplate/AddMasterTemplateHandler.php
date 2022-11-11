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

namespace App\Locodio\Application\Command\Model\AddMasterTemplate;

use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateType;
use App\Locodio\Domain\Model\User\UserRepository;

class AddMasterTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected UserRepository           $userRepo,
        protected MasterTemplateRepository $masterTemplateRepo
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(AddMasterTemplate $command): bool
    {
        $user = $this->userRepo->getById($command->getUserId());

        $masterTemplates = $this->masterTemplateRepo->getByUser($user);
        foreach ($masterTemplates as $masterTemplate) {
            $masterTemplate->setSequence($masterTemplate->getSequence() + 1);
            $this->masterTemplateRepo->save($masterTemplate);
        }

        $masterTemplate = MasterTemplate::make(
            $user,
            $this->masterTemplateRepo->nextIdentity(),
            TemplateType::from($command->getType()),
            $command->getName(),
            $command->getLanguage()
        );
        $this->masterTemplateRepo->save($masterTemplate);

        return true;
    }
}
