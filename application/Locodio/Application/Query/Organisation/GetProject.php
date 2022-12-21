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

namespace App\Locodio\Application\Query\Organisation;

use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModuleRMCollection;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class GetProject
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected DocumentorRepository  $documentorRepo,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function ById(int $id): ProjectRM
    {
        $project = $this->projectRepo->getById($id);
        $projectRM = ProjectRM::hydrateFromModel($project, true);

        // -- fill the modules with usages --------------------------------------
        $usageModules = new ModuleRMCollection();
        foreach ($projectRM->getModules()->getCollection() as $moduleRM) {
            $moduleRM->setUsages($this->domainModelRepo->countByModule($moduleRM->getId()));
            $usageModules->addItem($moduleRM);
        }
        $projectRM->setModuleCollection($usageModules);

        // -- fill the status with usages ---------------------------------------
        $usageStatus = new ModelStatusRMCollection();
        foreach ($projectRM->getStatus()->getCollection() as $statusRM) {
            $statusRM->setUsages($this->documentorRepo->countByModelStatus($statusRM->getId()));
            $usageStatus->addItem($statusRM);
        }
        $projectRM->setStatusCollection($usageStatus);

        return $projectRM;
    }

    public function SummaryById(int $id): ProjectRM
    {
        $project = $this->projectRepo->getById($id);
        return ProjectRM::hydrateFromModel($project);
    }
}
