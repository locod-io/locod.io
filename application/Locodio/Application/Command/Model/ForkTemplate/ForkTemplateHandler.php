<?php

namespace App\Locodio\Application\Command\Model\ForkTemplate;

use App\Locodio\Domain\Model\Model\MasterTemplate;
use App\Locodio\Domain\Model\Model\MasterTemplateFork;
use App\Locodio\Domain\Model\Model\MasterTemplateForkRepository;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\User\UserRepository;

class ForkTemplateHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected MasterTemplateRepository     $masterTemplateRepo,
        protected MasterTemplateForkRepository $masterTemplateForkRepo,
        protected UserRepository               $userRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    public function go(ForkTemplate $command): bool
    {
        $user = $this->userRepo->getById($command->getUserId());
        $sourceTemplate = $this->masterTemplateRepo->getById($command->getTemplateId());
        $targetTemplate = MasterTemplate::make(
            $user,
            $this->masterTemplateRepo->nextIdentity(),
            $sourceTemplate->getType(),
            $sourceTemplate->getName(),
            $sourceTemplate->getLanguage(),
        );
        $targetTemplate->change(
            $sourceTemplate->getType(),
            $sourceTemplate->getName(),
            $sourceTemplate->getLanguage(),
            $sourceTemplate->getTemplate(),
            false,
            $sourceTemplate->getDescription(),
            $sourceTemplate->getTags()
        );
        $this->masterTemplateRepo->save($targetTemplate);
        $forkLog = MasterTemplateFork::make(
            $this->masterTemplateForkRepo->nextIdentity(),
            $sourceTemplate->getUuid(),
            $targetTemplate->getUuid(),
            $user->getEmail()
        );
        $this->masterTemplateForkRepo->save($forkLog);
        return true;
    }
}
