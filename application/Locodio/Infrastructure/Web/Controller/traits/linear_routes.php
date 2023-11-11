<?php

namespace App\Locodio\Infrastructure\Web\Controller\traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait linear_routes
{
    /**
     * @throws \Exception
     */
    #[Route(
        '/api/project/{id}/linear-project/{uuid}',
        requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}', 'id' => '\d+'],
        methods: ['GET']
    )
    ]
    public function getLinearProjectDetail(int $id, string $uuid): Response
    {
        $response = $this->queryBus->getLinearProjectById($id, $uuid);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
