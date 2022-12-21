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

use App\Locodio\Application\Command\Model\UploadDocumentorImage\UploadDocumentorImage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

trait model_documentor_routes
{
    #[Route('/api/model/documentor/{type}/{subjectId}', requirements: ['subjectId' => '\d+'], methods: ['GET'])]
    public function getDocumentorByTypeAndSubjectId(string $type, int $subjectId): Response
    {
        $response = $this->queryBus->getDocumentorByTypeAndSubjectId($type, $subjectId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/documentor/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeDocumentor(int $id, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('documentor'));
        $response = $this->commandBus->changeDocumentor($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/documentor/{id}/status', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeDocumentorStatus(int $id, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('documentor'));
        $response = $this->commandBus->changeDocumentorStatus($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/documentor/{id}/upload', requirements: ['id' => '\d+'])]
    public function uploadImageForDocumentor(int $id, Request $request)
    {
        $response = false;
        $file = $request->files->get('documentorImage');
        if ($file) {
            $command = new UploadDocumentorImage(
                $id,
                $file,
                $this->uploadFolder
            );
            $response = $this->commandBus->uploadImageForDocumentor($command);
        }
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/documentor/{id}/image', requirements: ['id' => '\d+'])]
    public function streamImageForDocumentor(int $id, Request $request)
    {
        $documentor = $this->queryBus->getDocumentorById($id);
        $file = $this->uploadFolder . $documentor->getImage();
        return $this->file($file, 'image.png', ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/api/model/documentor/{id}/remove-image', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function removeImageForDocumentor(int $id): Response
    {
        $jsonCommand = new \stdClass();
        $jsonCommand->documentorId = $id;
        $response = $this->commandBus->removeImageForDocumentor($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
