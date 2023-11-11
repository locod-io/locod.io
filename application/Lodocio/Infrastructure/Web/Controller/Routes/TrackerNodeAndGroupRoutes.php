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

trait TrackerNodeAndGroupRoutes
{
    #[Route('/api/doc/tracker/node/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTrackerNodeById(int $id): JsonResponse
    {
        $response = $this->queryBus->getTrackerNodeById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/full', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getFullTrackerNodeById(int $id): JsonResponse
    {
        $response = $this->queryBus->getFullTrackerNodeById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/related-issues', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getNodeRelatedIssues(int $id): JsonResponse
    {
        $response = $this->queryBus->getIssuesByNodeId($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/name', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeNodeName(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeNodeName($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/description', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeNodeDescription(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeNodeDescription($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/related-issues', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeNodeRelatedIssues(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeNodeRelatedIssues($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/change-status', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeStatusNode(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeStatusNode($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteTrackerNode(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteTrackerNode($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // -- groups

    #[Route('/api/doc/tracker/node/group/{id}/name', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeGroupName(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeGroupName($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/group/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteTrackerNodeGroup(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteTrackerNodeGroup($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
