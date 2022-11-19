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

trait model_enum_option_routes
{
    #[Route('/api/model/enum-option/add', methods: ['POST'])]
    public function addEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum-option'));
        $response = $this->commandBus->addEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/order', methods: ['POST'])]
    public function orderEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('sequence'));
        $response = $this->commandBus->orderEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/{id}', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function changeEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum-option'));
        $response = $this->commandBus->changeEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/enum-option/{id}/delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEnumOption(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('enum-option'));
        $response = $this->commandBus->deleteEnumOption($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
