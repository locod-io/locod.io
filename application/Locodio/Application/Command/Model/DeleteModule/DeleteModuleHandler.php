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

namespace App\Locodio\Application\Command\Model\DeleteModule;

use App\Locodio\Application\traits\model_status_query;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\ModuleRepository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class DeleteModuleHandler
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected ModuleRepository $moduleRepo,
        protected DomainModelRepository $domainModelRepo,
        protected DocumentorRepository $documentorRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Handle
    // ———————————————————————————————————————————————————————————————

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function go(DeleteModule $command): bool
    {
        $module = $this->moduleRepo->getById($command->getId());
        $usages = $this->domainModelRepo->countByModule($module->getId());
        if ($usages > 0) {
            return false;
        }
        $documentor = $module->getDocumentor();
        if (!is_null($documentor)) {
            $this->documentorRepo->delete($documentor);
        }
        $this->moduleRepo->delete($module);

        return true;
    }
}
