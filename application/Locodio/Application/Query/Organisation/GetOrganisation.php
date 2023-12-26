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

use App\Linear\Application\Query\GetRoadmap;
use App\Linear\Application\Query\GetTeams;
use App\Linear\Application\Query\LinearConfig;
use App\Linear\Application\Query\Readmodel\ProjectReadModelCollection;
use App\Linear\Application\Query\Readmodel\RoadmapReadModelCollection;
use App\Linear\Application\Query\Readmodel\TeamReadModelCollection;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;
use App\Locodio\Infrastructure\Database\ProjectRepository;

class GetOrganisation
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected OrganisationRepository $organisationRepo,
        protected ProjectRepository      $projectRepository,
        protected LinearConfig           $linearConfig,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

    public function ById(int $id): OrganisationRM
    {
        return OrganisationRM::hydrateFromModel($this->organisationRepo->getById($id));
    }

    public function BySlug(string $slug): OrganisationRM
    {
        return OrganisationRM::hydrateFromModel($this->organisationRepo->getBySlug($slug));
    }

    public function ByCollection(OrganisationRMCollection $collection): OrganisationRMCollection
    {
        $result = new OrganisationRMCollection();
        foreach ($collection->getCollection() as $organisation) {
            $orgModel = $this->organisationRepo->getById($organisation->getId());
            $orgRM = OrganisationRM::hydrateFromModel($orgModel, true);
            $result->addItem($orgRM);
        }
        return $result;
    }

    public function RoadmapsByCollection(OrganisationRMCollection $collection): RoadmapReadModelCollection
    {
        $result = new RoadmapReadModelCollection();
        foreach ($collection->getCollection() as $organisation) {
            $tempResult = $this->FullRoadmapsByOrganisation($organisation->getId());
            foreach ($tempResult->getCollection() as $roadmapReadmodel) {
                $result->addItem($roadmapReadmodel);
            }
        }

        return $result;
    }

    // -- get teams --------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function TeamsByOrganisation(int $id): TeamReadModelCollection
    {
        $organisation = $this->organisationRepo->getById($id);
        $collection = new TeamReadModelCollection();
        if (strlen($organisation->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($organisation->getLinearApiKey());
            $getTeams = new GetTeams($this->linearConfig);
            $collection = $getTeams->All();
        }
        return $collection;
    }

    // -- get projects -------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function ProjectsByOrganisation(int $id): ProjectReadModelCollection
    {
        $organisation = $this->organisationRepo->getById($id);
        $collection = new ProjectReadModelCollection();
        if (strlen($organisation->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($organisation->getLinearApiKey());
            $getProject = new \App\Linear\Application\Query\GetProject($this->projectRepository, $this->linearConfig);
            $collection = $getProject->All();
        }

        return $collection;
    }

    // -- get roadmaps -------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function RoadmapsByOrganisation(int $id): RoadmapReadModelCollection
    {
        $organisation = $this->organisationRepo->getById($id);
        $collection = new RoadmapReadModelCollection();
        if (strlen($organisation->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($organisation->getLinearApiKey());
            $getRoadmap = new GetRoadmap($this->linearConfig);
            $collection = $getRoadmap->All();
        }

        return $collection;
    }

    public function FullRoadmapsByOrganisation(int $id): RoadmapReadModelCollection
    {
        $organisation = $this->organisationRepo->getById($id);
        $collection = new RoadmapReadModelCollection();
        if (strlen($organisation->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($organisation->getLinearApiKey());
            $getRoadmap = new GetRoadmap($this->linearConfig);
            foreach ($organisation->getProjects() as $project) {
                foreach ($project->getRelatedRoadmaps() as $roadmap) {
                    $collection->addItem($getRoadmap->ByUuid($roadmap['id']));
                }
            }
        }

        return $collection;
    }

}
