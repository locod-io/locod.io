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

trait model_domain_model_routes
{
    #[Route('/api/model/domain-model/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getDomainModelById(int $id): Response
    {
        $response = $this->queryBus->getDomainModelById($id);
        usleep(abs($this->defaultSleep));
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/add', methods: ['POST'])]
    public function addDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('domainModel'));
        $response = $this->commandBus->addDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/order', methods: ['POST'])]
    public function orderDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('domainModel'));
        $response = $this->commandBus->changeDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/domain-model/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteDomainModel(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('domainModel'));
        $response = $this->commandBus->deleteDomainModel($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
