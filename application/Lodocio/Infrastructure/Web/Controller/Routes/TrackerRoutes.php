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

use App\Lodocio\Application\Command\Tracker\UploadTrackerFile\UploadTrackerFile;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    #[Route('/api/doc/tracker/node/{id}/upload-file', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function uploadTrackerFile(int $id, Request $request): JsonResponse
    {
        $response = false;
        $file = $request->files->get('trackerNodeImage');
        if ($file) {
            $command = new UploadTrackerFile(
                $id,
                $file,
                $this->uploadFolder
            );
            $response = $this->commandBus->uploadImageForTrackerNode($command);
        }
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/{id}/upload-figma-file', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function uploadWikiFigmaFile(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->uploadFigmaExportImage($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/file/{id}/image', requirements: ['id' => '\d+'])]
    public function streamImageForTrackerFile(int $id, Request $request): BinaryFileResponse
    {
        $trackerFile = $this->queryBus->getTrackerFileById($id);
        $file = $this->uploadFolder . $trackerFile->getSrcPath();
        return $this->file($file, 'image.png', ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/api/doc/tracker/node/file/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function deleteTrackerFile(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteTrackerFile($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/tracker/node/file/order', methods: ['POST', 'PUT'])]
    public function orderTrackerFile(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->orderTrackerFile($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

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

    /**
     * @throws Exception
     */
    #[Route('/api/doc/tracker/add-project-document', methods: ['POST', 'PUT'])]
    public function addProjectDocument(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addProjectDocument($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/doc/tracker/delete-project-document', methods: ['POST', 'PUT'])]
    public function deleteProjectDocument(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteProjectDocument($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
