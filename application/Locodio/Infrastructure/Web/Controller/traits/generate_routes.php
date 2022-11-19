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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait generate_routes
{
    #[Route(
        '/api/model/template/{id}/generate/{subjectId}',
        requirements: ['id' => '\d+', 'subjectId' => '\d+'],
        methods: ['GET']
    )]
    public function generateTemplateBySubjectId(
        int $id,
        int $subjectId
    ): Response {
        $response = $this->queryBus->generateTemplateBySubjectId($id, $subjectId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
