<?php

declare(strict_types=1);

namespace App\Figma\Application\Query\Files;

use App\Figma\FigmaConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GetFile
{
    protected Client $client;
    protected array $headers;

    public function __construct(protected FigmaConfig $config)
    {
        $this->client = new Client();
        $this->headers = ['headers' => ['X-FIGMA-TOKEN' => $this->config->getKey()]];
    }

    /**
     * @throws GuzzleException
     */
    public function getNodeIds(string $documentKey): array
    {
        $apiUrl = "{$this->config->getEndPoint()}/files/{$documentKey}";
        $response = $this->client->request('GET', $apiUrl, $this->headers);
        $responseBody = $response->getBody()->getContents();
        $responseObject = json_decode($responseBody);
        $result = [];
        if ($responseObject->document->children[0]->type === 'CANVAS') {
            if ($responseObject->document->children[0]->children[0]->type === 'SECTION'
                || $responseObject->document->children[0]->children[0]->type === 'FRAME') {
                foreach ($responseObject->document->children[0]->children as $section) {
                    $result[] = $section->id;
                }
            } else {
                $result[] = $responseObject->document->children[0]->id;
            }
        } else {
            $result[] = '0:1';
        }
        return $result;
    }

}
