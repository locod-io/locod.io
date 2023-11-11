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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

trait ProjectRoutes
{
    /**
     * @throws \Exception
     */
    #[Route('/api/doc/project/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getDocProjectById(int $id, Request $request): JsonResponse
    {
        $response = $this->queryBus->getDocProjectById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
