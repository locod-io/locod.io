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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

trait TrackersFileRoutes
{
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

}
