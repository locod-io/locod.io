<?php

namespace App\Lodocio\Infrastructure\Web\Controller\Routes;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

trait ProjectDocumentRoutes
{

    /**
     * @throws \Exception
     */
    #[Route('/api/doc/tracker/add-project-document', methods: ['POST', 'PUT'])]
    public function addProjectDocument(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addProjectDocument($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/doc/tracker/delete-project-document', methods: ['POST', 'PUT'])]
    public function deleteProjectDocument(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteProjectDocument($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}