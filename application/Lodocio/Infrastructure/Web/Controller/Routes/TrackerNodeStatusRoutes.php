<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Infrastructure\Web\Controller\Routes;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait TrackerNodeStatusRoutes
{
    #[Route('/api/doc/tracker/node-status/order', methods: ['POST', 'PUT'])]
    public function orderTrackerNodeStatus(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->orderTrackerNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node-status/add', methods: ['POST', 'PUT'])]
    public function addTrackerNodeStatus(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addTrackerNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{trackerId}/node-status/workflow', methods: ['GET'])]
    public function getModelStatusWorkflow(int $trackerId): Response
    {
        $response = $this->queryBus->getTrackerNodeStatusWorkflow($trackerId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{trackerId}/node-status/workflow', methods: ['POST'])]
    public function saveModelStatusWorkflow(int $trackerId, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('workflow'));
        $response = $this->commandBus->saveTrackerNodeStatusWorkflow($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node-status/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTrackerNodeStatusById(int $id, Request $request): JsonResponse
    {
        $response = $this->queryBus->getTrackerNodeStatusById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node-status/{id}', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeTrackerNodeStatus(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeTrackerNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/status/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteTrackerNodeStatus(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteTrackerNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
