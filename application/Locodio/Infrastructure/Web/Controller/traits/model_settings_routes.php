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

trait model_settings_routes
{
    #[Route('/api/model/model-settings', methods: ['POST'])]
    public function changeModelSettings(Request $request): Response
    {
        $jsonCommand = json_decode($request->request->get('modelSettings'));
        $response = $this->commandBus->changeModelSettings($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
