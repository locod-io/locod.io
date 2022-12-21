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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait model_module_routes
{
    #[Route('/api/model/module/add', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function addModule(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('module'));
        $response = $this->commandBus->addModule($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/module/order', methods: ['POST'])]
    public function orderModule(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderModule($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/module/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeModule(int $id, Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('module'));
        $response = $this->commandBus->changeModule($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/api/model/module/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteModule(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('module'));
        $response = $this->commandBus->deleteModule($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
