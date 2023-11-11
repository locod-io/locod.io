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

trait TrackerRoutes
{
    #[Route('/api/doc/tracker/order', methods: ['POST', 'PUT'])]
    public function orderTracker(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->orderTrackers($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/add', methods: ['POST', 'PUT'])]
    public function addTracker(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addTracker($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTrackerById(int $id, Request $request): JsonResponse
    {
        $response = $this->queryBus->getTrackerById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}/full', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getFullTrackerById(int $id): JsonResponse
    {
        $response = $this->queryBus->getFullTrackerById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}/issues', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTrackerIssues(int $id): JsonResponse
    {
        $response = $this->queryBus->getIssuesByTrackerId($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}/pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function renderTrackerPdf(int $id): JsonResponse
    {
        $response = $this->queryBus->renderTrackerPdf($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}/sync-structure', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function syncTrackerStructure(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->syncTrackerStructure($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeTracker(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeTracker($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteTracker(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteTracker($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
