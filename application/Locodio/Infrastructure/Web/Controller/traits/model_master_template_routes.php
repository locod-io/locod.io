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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

trait model_master_template_routes
{
    #[Route('/api/model/master-template/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getMasterTemplateById(int $id): Response
    {
        $response = $this->queryBus->getMasterTemplateById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/{id}/download', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function downloadMasterTemplateById(int $id): Response
    {
        $response = $this->queryBus->getMasterTemplateById($id);
        $tempExportFile = $this->appKernel->getCacheDir() . '/' . $response->getUuid() . '.tmp';
        file_put_contents($tempExportFile, $response->getTemplate());
        return $this->file($tempExportFile, $response->getName() . '.' . strtolower($response->getLanguage()) . '.twig', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    #[Route('/api/model/master-template/add', methods: ['POST'])]
    public function addMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('masterTemplate'));
        $response = $this->commandBus->addMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/order', methods: ['POST'])]
    public function orderMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/change-content', methods: ['POST'])]
    public function changeContentMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('config'));
        $response = $this->commandBus->changeMasterTemplateContents($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('masterTemplate'));
        $response = $this->commandBus->changeMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('masterTemplate'));
        $response = $this->commandBus->deleteMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/master-template/browse', methods: ['GET'])]
    public function browseMasterTemplates(): Response
    {
        $response = $this->queryBus->getPublicTemplates();
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
