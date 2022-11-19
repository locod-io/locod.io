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

namespace App\Locodio\Infrastructure\Web\Controller\traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait model_query_routes
{
    #[Route('/api/model/query/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getQueryById(int $id): Response
    {
        $response = $this->queryBus->getQueryById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/add', methods: ['POST'])]
    public function addQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('query'));
        $response = $this->commandBus->addQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/order', methods: ['POST'])]
    public function orderQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('query'));
        $response = $this->commandBus->changeQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/query/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteQuery(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('query'));
        $response = $this->commandBus->deleteQuery($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
