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

use App\Locodio\Application\Query\Linear\Readmodel\ProjectReadModel;
use App\Locodio\Application\Query\Linear\Readmodel\ProjectReadModelCollection;
use App\Locodio\Domain\Model\Organisation\ProjectRepository;
use Softonic\GraphQL\Client;
use Softonic\GraphQL\ClientBuilder;

class GetProject
{
    private Client $linearClient;

    public function __construct(
        protected ProjectRepository $projectRepository,
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

    /**
     * @throws \Exception
     */
    public function All(): ProjectReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $result = [];
        $query = <<<'QUERY'
query Projects {
  projects {
    nodes {
      id
      name
      color
      url      
      teams {
        nodes {
          id
          name
          color
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
            $result = $response->getData();
        }
        $collection = new ProjectReadModelCollection();
        foreach ($result['projects']['nodes'] as $project) {
            $collection->addItem(ProjectReadModel::hydrateFromModel($project));
        }

        return $collection;
    }

    /**
     * @throws \Exception
     */
    public function ByUuid(int $projectId, string $uuid): array
    {
        $project = $this->projectRepository->getById($projectId);
        if (strlen($project->getOrganisation()->getLinearApiKey()) !== 0) {
            $this->linearConfig->setKey($project->getOrganisation()->getLinearApiKey());
        }
        LinearConfig::checkConfig($this->linearConfig);
        $this->linearClient = ClientBuilder::build(
            $this->linearConfig->getEndPoint(),
            ['headers' => [
                'Authorization' => $this->linearConfig->getKey(),
                'Content-Type' => 'application/json',
            ],]
        );
        $query = <<<QUERY
query Project {
  project(id: "$uuid") {
    id
    name
    color
    url
    description
    state
    startDate
    targetDate
    lead {
      name
    }
    members {
      nodes {
        name
      }
    }
    projectUpdates {
      nodes {
        createdAt
        body
        health
      }
    }
    projectMilestones {
      nodes {
        name
        targetDate
      }
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
        return $result;
    }
}
