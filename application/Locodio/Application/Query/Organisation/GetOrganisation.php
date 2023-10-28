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

use App\Locodio\Application\Query\Linear\GetTeams;
use App\Locodio\Application\Query\Linear\LinearConfig;
use App\Locodio\Application\Query\Linear\Readmodel\TeamReadModelCollection;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRM;
use App\Locodio\Application\Query\Organisation\Readmodel\OrganisationRMCollection;
use App\Locodio\Domain\Model\Organisation\OrganisationRepository;

class GetOrganisation
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected OrganisationRepository $organisationRepo,
        protected LinearConfig           $linearConfig,
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

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
}
