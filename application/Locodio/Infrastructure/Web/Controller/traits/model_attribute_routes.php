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
use Symfony\Component\Routing\Annotation\Route;

trait model_attribute_routes
{
    #[Route('/api/model/attribute/add', methods: ['POST'])]
    public function addAttribute(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('attribute'));
        $response = $this->commandBus->addAttribute($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/attribute/order', methods: ['POST'])]
    public function orderAttribute(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderAttribute($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/attribute/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeAttribute(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('attribute'));
        $response = $this->commandBus->changeAttribute($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/attribute/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteAttribute(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('attribute'));
        $response = $this->commandBus->deleteAttribute($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
