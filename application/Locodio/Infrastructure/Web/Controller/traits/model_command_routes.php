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

trait model_command_routes
{
    #[Route('/api/model/command/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getCommandById(int $id): Response
    {
        $response = $this->queryBus->getCommandById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/add', methods: ['POST'])]
    public function addCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->addCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/order', methods: ['POST'])]
    public function orderCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/command/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteCommand(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->deleteCommand($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
