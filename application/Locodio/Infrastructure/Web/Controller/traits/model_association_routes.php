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

trait model_association_routes
{
    #[Route('/api/model/association/add', methods: ['POST'])]
    public function addAssociation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('association'));
        $response = $this->commandBus->addAssociation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/association/order', methods: ['POST'])]
    public function orderAssociations(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderAssociation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/association/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeAssociation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('association'));
        $response = $this->commandBus->changeAssociation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/association/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteAssociation(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('association'));
        $response = $this->commandBus->deleteAssociation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
