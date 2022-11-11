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

namespace App\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRM;
use App\Locodio\Application\Query\Model\Readmodel\MasterTemplateRMCollection;
use App\Locodio\Domain\Model\Model\MasterTemplateRepository;
use App\Locodio\Domain\Model\User\UserRepository;

class GetMasterTemplate
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected MasterTemplateRepository $masterTemplateRepo,
        protected UserRepository           $userRepo
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

    public function ById(int $id): MasterTemplateRM
    {
        $masterTemplate = $this->masterTemplateRepo->getById($id);
        return MasterTemplateRM::hydrateFromModel($masterTemplate, true);
    }

    public function ByUserId(int $userId): MasterTemplateRMCollection
    {
        $user = $this->userRepo->getById($userId);
        $masterTemplates = $this->masterTemplateRepo->getByUser($user);
        $collection = new MasterTemplateRMCollection();
        foreach ($masterTemplates as $masterTemplate) {
            $collection->addItem(MasterTemplateRM::hydrateFromModel($masterTemplate));
        }
        return $collection;
    }

    public function Public(): MasterTemplateRMCollection
    {
        $masterTemplates = $this->masterTemplateRepo->getPublicTemplates();
        $collection = new MasterTemplateRMCollection();
        foreach ($masterTemplates as $masterTemplate) {
            $collection->addItem(MasterTemplateRM::hydrateFromModel($masterTemplate, true));
        }
        return $collection;
    }
}
