<?php

declare(strict_types=1);

namespace App\Locodio\Application\Query\Linear;

use App\Locodio\Application\Query\Linear\Readmodel\DocumentReadModel;
use App\Locodio\Application\Query\Linear\Readmodel\DocumentReadModelCollection;
use Softonic\GraphQL\Client;
use Softonic\GraphQL\ClientBuilder;

class GetDocuments
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

    // -- check document by id -----------------------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function CheckById(string $id): bool
    {
        LinearConfig::checkConfig($this->linearConfig);
        $query = <<<QUERY
{
  document(id: "$id") {
    id    
  }
}
QUERY;
        $response = $this->linearClient->query($query, []);
        if ($response->hasErrors()) {
            return false;
        } else {
            return true;
        }
    }


    // -- get documents for different projects -------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function ByProjects(array $projects): DocumentReadModelCollection
    {
        $finalCollection = new DocumentReadModelCollection();
        foreach ($projects as $project) {
            $tempCollection = $this->ByProject($project['id']);
            foreach ($tempCollection->getCollection() as $document) {
                $finalCollection->addItem($document);
            }
        }
        return $finalCollection;
    }

    // -- get documents by project -------------------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function ByProject(string $id): DocumentReadModelCollection
    {
        LinearConfig::checkConfig($this->linearConfig);
        $result = [];
        $query = <<<QUERY
{
  project(id: "$id") {
    id
    documents {
      nodes {
        id
        title
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
        $collection = new DocumentReadModelCollection();
        foreach ($result['project']['documents']['nodes'] as $document) {
            $collection->addItem(DocumentReadModel::hydrateFromModel($document, $result['project']['id']));
        }
        return $collection;
    }

}