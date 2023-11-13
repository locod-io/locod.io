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

namespace App\Linear\Application\Query;

use App\Linear\Application\Query\Readmodel\TeamReadModel;
use App\Linear\Application\Query\Readmodel\TeamReadModelCollection;
use Softonic\GraphQL\Client;
use Softonic\GraphQL\ClientBuilder;

class GetTeams
{
    private Client $linearClient;

    public function __construct(
        protected LinearConfig $linearConfig
    ) {
        $this->linearClient = ClientBuilder::build(
            $this->linearConfig->getEndPoint(),
            ['headers' => [
                'Authorization' => $this->linearConfig->getKey(),
                'Content-Type' => 'application/json',
            ],]
        );
    }

    public function All(): TeamReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $result = [];
        $query = <<<'QUERY'
query Teams {
  teams {
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
        $collection = new TeamReadModelCollection();
        foreach ($result['teams']['nodes'] as $team) {
            $collection->addItem(TeamReadModel::hydrateFromModel($team));
        }
        return $collection;
    }

}

// labels {
//    nodes {
//        name
//          color
//        }
// }
