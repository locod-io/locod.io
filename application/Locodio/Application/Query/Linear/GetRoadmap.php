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

namespace App\Locodio\Application\Query\Linear;

use App\Locodio\Application\Query\Linear\Readmodel\RoadmapReadModel;
use App\Locodio\Application\Query\Linear\Readmodel\RoadmapReadModelCollection;
use Softonic\GraphQL\Client;
use Softonic\GraphQL\ClientBuilder;

class GetRoadmap
{

    private Client $linearClient;

    public function __construct(
        protected LinearConfig $linearConfig
    )
    {
        $this->linearClient = ClientBuilder::build(
            $this->linearConfig->getEndPoint(),
            ['headers' => [
                'Authorization' => $this->linearConfig->getKey(),
                'Content-Type' => 'application/json',
            ],]
        );
    }

    // -- get all roadmaps ---------------------------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function All(): RoadmapReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $query = <<<'QUERY'
{
  roadmaps {
    nodes {
      id
      name      
    }
  }
}
QUERY;
        $response = $this->linearClient->query($query, []);
        if ($response->hasErrors()) {
            throw new \Exception('Something went wrong asking Linear.');
        } else {
            $result = $response->getData();
        }
        $collection = new RoadmapReadModelCollection();
        foreach ($result['roadmaps']['nodes'] as $project) {
            $collection->addItem(RoadmapReadModel::hydrateFromModel($project));
        }
        return $collection;
    }

    // -- get detail of a roadmap --------------------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function ByUuid(string $id): RoadmapReadModel
    {
        LinearConfig::checkConfig($this->linearConfig);
        $query = <<<QUERY
{
   roadmap(id: "$id") {
     id
     name
     description
     projects {
       nodes {
         id
         color
         url
         name
         description         
         startDate         
         targetDate
         state
         sortOrder
         lead {
           name
         }
       }
     }     
  }
}
QUERY;
        $response = $this->linearClient->query($query, []);
        if ($response->hasErrors()) {
            throw new \Exception('Something went wrong asking Linear.');
        } else {
            return RoadmapReadModel::hydrateFromModel($response->getData()['roadmap'], true);
        }
    }

}