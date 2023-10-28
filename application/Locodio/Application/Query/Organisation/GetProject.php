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

use App\Locodio\Application\Query\Linear\GetIssues;
use App\Locodio\Application\Query\Linear\LinearConfig;
use App\Locodio\Application\Query\Linear\Readmodel\IssueCacheReadModelCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModelStatusRMCollection;
use App\Locodio\Application\Query\Model\Readmodel\ModuleRMCollection;
use App\Locodio\Application\Query\Organisation\Readmodel\ProjectRM;
use App\Locodio\Domain\Model\Model\DocumentorRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;

class GetProject
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected ProjectRepository     $projectRepo,
        protected DomainModelRepository $domainModelRepo,
        protected DocumentorRepository  $documentorRepo,
        protected LinearConfig          $linearConfig,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

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

    /**
     * @throws \Exception
     */
    public function ListIssuesById(int $id): IssueCacheReadModelCollection
    {
        $project = $this->projectRepo->getById($id);
        $collection = new IssueCacheReadModelCollection();
        if (strlen($project->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($project->getOrganisation()->getLinearApiKey());
            $getIssues = new GetIssues($this->linearConfig);
            $collection = $getIssues->ByTeams($project->getModelSettings()->getLinearTeams());
        }

        return $collection;
    }

    public function SummaryById(int $id): ProjectRM
    {
        $project = $this->projectRepo->getById($id);
        return ProjectRM::hydrateFromModel($project);
    }
}
