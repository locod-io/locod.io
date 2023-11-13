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

use App\Linear\Application\Query\Readmodel\IssueCacheReadModel;
use App\Linear\Application\Query\Readmodel\IssueCacheReadModelCollection;
use App\Linear\Application\Query\Readmodel\IssueReadModel;
use App\Linear\Application\Query\Readmodel\IssueReadModelCollection;
use Softonic\GraphQL\Client;
use Softonic\GraphQL\ClientBuilder;

class GetIssues
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

    /**
     * @throws \Exception
     */
    public function ByTeams(array $teams): IssueCacheReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $finalCollection = new IssueCacheReadModelCollection();
        foreach ($teams as $team) {
            $tempCollection = $this->CacheByTeam($team['id']);
            foreach ($tempCollection->getCollection() as $issue) {
                $finalCollection->addItem($issue);
            }
        }
        return $finalCollection;
    }

    public function CacheByTeam(string $id): IssueCacheReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $result = [];
        $query = <<<QUERY
query Team {
  team(id: "$id") {
    issues {
      nodes {
        id
        title                
        identifier        
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
        $collection = new IssueCacheReadModelCollection();
        foreach ($result['team']['issues']['nodes'] as $issue) {
            $collection->addItem(IssueCacheReadModel::hydrateFromModel($issue));
        }
        return $collection;
    }

    public function ByTeam(string $id): IssueReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $result = [];
        $query = <<<QUERY
query Team {
  team(id: "$id") {
    id
    name
    issues {
      nodes {
        id
        title                
        identifier
        url
        state {
            id
            type
            color
            position
            name
        }
        assignee {
          id
          name
        }
        createdAt
        completedAt
        archivedAt
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
        $collection = new IssueReadModelCollection();
        foreach ($result['team']['issues']['nodes'] as $issue) {
            $collection->addItem(IssueReadModel::hydrateFromModel($issue));
        }
        return $collection;
    }

    public function ByIssueId(string $id): IssueReadModel
    {
        LinearConfig::checkConfig($this->linearConfig);
        $result = [];
        $query = <<<QUERY
query Issue {
        issue(id: "$id") {
            id
            title                
            identifier
            url
            description
            state {
                id
                type
                color
                position
                name
            }
            assignee {
                id
                name
            }
            createdAt
            completedAt
            archivedAt
            children {
                nodes {
                    id
                    title                
                    identifier
                    url
                    description
                    state {
                        id
                        type
                        color
                        position
                        name
                    }
                    assignee {
                        id
                        name
                    }
                    createdAt
                    completedAt
                    archivedAt
                }
            }            
            comments {
                nodes {
                    id
                    body
                    createdAt
                    url
                    user {
                        id
                        name
                    }
                    parent {
                        id
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
        $model = IssueReadModel::hydrateFromModel($result['issue'], true);
        return $model;
    }

}
