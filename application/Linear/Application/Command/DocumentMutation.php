<?php

declare(strict_types=1);

namespace App\Linear\Application\Command;

use App\Linear\Application\Query\LinearConfig;
use App\Lodocio\Domain\Model\Tracker\TrackerRelatedProjectDocument;
use App\Lodocio\Domain\Model\Wiki\WikiRelatedProjectDocument;
use Softonic\GraphQL\Client;
use Softonic\GraphQL\ClientBuilder;
use Symfony\Component\Uid\Uuid;

class DocumentMutation
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

    // -- create document ----------------------------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function createDocument(
        string $projectId,
        string $title,
        string $content,
        DocumentSubject $documentSubject = DocumentSubject::TRACKER
    ): TrackerRelatedProjectDocument|WikiRelatedProjectDocument {
        LinearConfig::checkConfig($this->linearConfig);
        $mutation = <<<'MUTATION'
mutation ($input: DocumentCreateInput!) {
  documentCreate (input: $input) {
    document {
        id
        title
        project {
            id
        }
    }
  }
}
MUTATION;
        $variables = ['input' => ["title" => $title, "content" => $content, "projectId" => $projectId]];
        $response = $this->linearClient->query($mutation, $variables);
        if ($response->hasErrors()) {
            throw new \Exception('Something went wrong asking Linear.');
        } else {
            $data = $response->getData();
            switch ($documentSubject) {
                case DocumentSubject::WIKI:
                    return WikiRelatedProjectDocument::make(
                        Uuid::v4(),
                        $projectId,
                        $data['documentCreate']['document']['id'],
                        $title
                    );
                    break;
                default:
                    return TrackerRelatedProjectDocument::make(
                        Uuid::v4(),
                        $projectId,
                        $data['documentCreate']['document']['id'],
                        $title
                    );
                    break;
            }
        }
    }

    // -- delete document ----------------------------------------------------------------------------------------------

    /**
     * @throws \Exception
     */
    public function deleteDocument(string $documentId): bool
    {
        // documentDelete ( id: String! ) : DeletePayload!

        LinearConfig::checkConfig($this->linearConfig);
        $mutation = <<<'MUTATION'
mutation ($id: String!) {
  documentDelete (id: $id) {
    success
  }
}
MUTATION;
        $variables = ['id' => $documentId];
        $response = $this->linearClient->query($mutation, $variables);
        return true;
    }

    // -- update document ----------------------------------------------------------------------------------------------
    //    public function updateDocument(string $documentId, string $title, string $content): DocumentReadModel
    //    {
    // documentUpdate ( input: DocumentUpdateInput!, id: String! ) :
    // DocumentUpdateInput (title, content)
    //    }


}
