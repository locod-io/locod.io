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

use App\Lodocio\Application\Command\Wiki\UploadWikiFile\UploadWikiFile;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

trait WikiRoutes
{
    #[Route('/api/doc/{id}/wiki', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getWikiByDocId(int $id, Request $request): JsonResponse
    {
        $response = $this->queryBus->getWikiByDocProjectId($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    //    #[Route('/api/doc/wiki/order', methods: ['POST', 'PUT'])]
    //    public function orderWiki(Request $request): JsonResponse
    //    {
    //        $jsonCommand = json_decode($request->request->get('command'));
    //        $response = $this->commandBus->orderWikis($jsonCommand);
    //        return new JsonResponse($response, 200, $this->apiAccess);
    //    }

    //    #[Route('/api/doc/wiki/add', methods: ['POST', 'PUT'])]
    //    public function addWiki(Request $request): JsonResponse
    //    {
    //        $jsonCommand = json_decode($request->request->get('command'));
    //        $response = $this->commandBus->addWiki($jsonCommand);
    //        return new JsonResponse($response, 200, $this->apiAccess);
    //    }

    #[Route('/api/doc/wiki/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getWikiById(int $id, Request $request): JsonResponse
    {
        $response = $this->queryBus->getWikiById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{id}/full', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getFullWikiById(int $id): JsonResponse
    {
        $response = $this->queryBus->getFullWikiById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{id}/issues', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getWikiIssues(int $id): JsonResponse
    {
        $response = $this->queryBus->getIssuesByWikiId($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{id}/pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function renderWikiPdf(int $id): JsonResponse
    {
        $response = $this->queryBus->renderWikiPdf($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{id}/sync-structure', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function syncWikiStructure(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->syncWikiStructure($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{id}', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeWiki(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeWiki($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteWiki(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteWiki($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/upload-file', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function uploadWikiFile(int $id, Request $request): JsonResponse
    {
        $response = false;
        $file = $request->files->get('wikiNodeImage');
        if ($file) {
            $command = new UploadWikiFile(
                $id,
                $file,
                $this->uploadFolder
            );
            $response = $this->commandBus->uploadImageForWikiNode($command);
        }
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/upload-figma-file', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function uploadWikiFigmaFile(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->uploadFigmaExportImage($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/file/{id}/image', requirements: ['id' => '\d+'])]
    public function streamImageForWikiFile(int $id, Request $request): BinaryFileResponse
    {
        $wikiFile = $this->queryBus->getWikiFileById($id);
        $file = $this->uploadFolder . $wikiFile->getSrcPath();
        return $this->file($file, 'image.png', ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/api/doc/wiki/node/file/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function deleteWikiFile(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteWikiFile($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/file/order', methods: ['POST', 'PUT'])]
    public function orderWikiFile(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->orderWikiFile($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node-status/order', methods: ['POST', 'PUT'])]
    public function orderWikiNodeStatus(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->orderWikiNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node-status/add', methods: ['POST', 'PUT'])]
    public function addWikiNodeStatus(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addWikiNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{wikiId}/node-status/workflow', methods: ['GET'])]
    public function getModelStatusWorkflow(int $wikiId): Response
    {
        $response = $this->queryBus->getWikiNodeStatusWorkflow($wikiId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/{wikiId}/node-status/workflow', methods: ['POST'])]
    public function saveModelStatusWorkflow(int $wikiId, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('workflow'));
        $response = $this->commandBus->saveWikiNodeStatusWorkflow($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node-status/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getWikiNodeStatusById(int $id, Request $request): JsonResponse
    {
        $response = $this->queryBus->getWikiNodeStatusById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node-status/{id}', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeWikiNodeStatus(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeWikiNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/status/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteWikiNodeStatus(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteWikiNodeStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getWikiNodeById(int $id): JsonResponse
    {
        $response = $this->queryBus->getWikiNodeById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/full', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getFullWikiNodeById(int $id): JsonResponse
    {
        $response = $this->queryBus->getFullWikiNodeById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/related-issues', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getNodeRelatedIssues(int $id): JsonResponse
    {
        $response = $this->queryBus->getIssuesByNodeId($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/name', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeNodeName(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeNodeName($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/description', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeNodeDescription(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeNodeDescription($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/related-issues', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeNodeRelatedIssues(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeNodeRelatedIssues($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/change-status', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeStatusNode(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeStatusNode($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteWikiNode(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteWikiNode($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    // -- groups

    #[Route('/api/doc/wiki/node/group/{id}/name', requirements: ['id' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeGroupName(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeGroupName($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/doc/wiki/node/group/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST', 'PUT', 'DELETE', 'OPTIONS'])]
    public function deleteWikiNodeGroup(int $id, Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteWikiNodeGroup($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/doc/wiki/add-project-document', methods: ['POST', 'PUT'])]
    public function addProjectDocument(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addProjectDocument($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/doc/wiki/delete-project-document', methods: ['POST', 'PUT'])]
    public function deleteProjectDocument(Request $request): JsonResponse
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteProjectDocument($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
