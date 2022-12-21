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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait model_status_routes
{
    #[Route('/api/model/model-status/add', methods: ['POST'])]
    public function addModelStatus(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('modelStatus'));
        $response = $this->commandBus->addModelStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/model-status/order', methods: ['POST'])]
    public function orderModelStatus(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderModelStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/model-status/{projectId}/workflow', methods: ['GET'])]
    public function getModelStatusWorkflow(int $projectId): Response
    {
        $response = $this->queryBus->getModelStatusWorkflowByProject($projectId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/model-status/{projectId}/workflow', methods: ['POST'])]
    public function saveModelStatusWorkflow(int $projectId, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('workflow'));
        $response = $this->commandBus->saveModelStatusWorkflow($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/model-status/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeModelStatus(int $id, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('modelStatus'));
        $response = $this->commandBus->changeModelStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/model-status/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getModelStatusById(int $id): Response
    {
        $response = $this->queryBus->getModelStatusById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/model-status/project/{projectId}', requirements: ['projectId' => '\d+'], methods: ['GET'])]
    public function getModelStatusByProjectId(int $projectId): Response
    {
        $response = $this->queryBus->getModelStatusByProject($projectId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/api/model/model-status/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteModelStatus(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('modelStatus'));
        $response = $this->commandBus->deleteModelStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
