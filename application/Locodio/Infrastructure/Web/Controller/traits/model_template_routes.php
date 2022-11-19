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

trait model_template_routes
{
    #[Route('/api/model/template/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTemplateById(int $id): Response
    {
        $response = $this->queryBus->getTemplateById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}/download', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function downloadTemplateById(int $id): Response
    {
        $response = $this->queryBus->getTemplateById($id);
        $tempExportFile = $this->appKernel->getCacheDir() . '/' . $response->getUuid() . '.tmp';
        file_put_contents($tempExportFile, $response->getTemplate());
        return $this->file($tempExportFile, $response->getName() . '.' . strtolower($response->getLanguage()) . '.twig', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    #[Route('/api/model/template/add', methods: ['POST'])]
    public function addTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('template'));
        $response = $this->commandBus->addTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/order', methods: ['POST'])]
    public function orderTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/import', methods: ['POST'])]
    public function importTemplatesFromMasterTemplates(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('import'));
        $response = $this->commandBus->importTemplatesFromMasterTemplates($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/change-content', methods: ['POST'])]
    public function changeContentTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('config'));
        $response = $this->commandBus->changeTemplateContents($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('template'));
        $response = $this->commandBus->changeTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}/export', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function exportTemplateToMasterTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('export'));
        $response = $this->commandBus->exportTemplateToMasterTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/template/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteTemplate(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('template'));
        $response = $this->commandBus->deleteTemplate($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
