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

trait model_enum_routes
{
    #[Route('/api/model/enum/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getEnumById(int $id): Response
    {
        $response = $this->queryBus->getEnumById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/add', methods: ['POST'])]
    public function addEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum'));
        $response = $this->commandBus->addEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/order', methods: ['POST'])]
    public function orderEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum'));
        $response = $this->commandBus->changeEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEnum(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum'));
        $response = $this->commandBus->deleteEnum($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
