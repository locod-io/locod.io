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

trait lists_routes
{
    #[Route(
        '/api/model/enum-values',
        requirements: ['id' => '\d+', 'subjectId' => '\d+'],
        methods: ['GET']
    )]
    public function getEnumValues(): Response
    {
        $response = $this->queryBus->getEnumValues();
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
