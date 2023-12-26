<?php

declare(strict_types=1);

namespace App\Figma\Application\Query\Files;

use App\Figma\FigmaConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GetImages
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
    public function forDocument(string $documentKey, float $scale = 1, string $format = 'png'): array
    {
        $getFile = new GetFile($this->config);
        $documentIds = $getFile->getNodeIds($documentKey);
        $documentIdsString = implode(',', $documentIds);
        $result = [];
        $apiUrl = "{$this->config->getEndPoint()}/images/{$documentKey}?ids={$documentIdsString}&scale={$scale}&format={$format}";
        $response = $this->client->request('GET', $apiUrl, $this->headers);
        $responseBody = $response->getBody()->getContents();
        $responseObject = json_decode($responseBody, true);
        return $responseObject['images'];
    }

}
