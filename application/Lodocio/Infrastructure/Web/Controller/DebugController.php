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

namespace App\Lodocio\Infrastructure\Web\Controller;

use App\Figma\Application\Query\Files\GetFile;
use App\Figma\Application\Query\Files\GetImages;
use App\Figma\FigmaConfig;
use GuzzleHttp\Exception\GuzzleException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    /**
     * @throws \Exception
     * @throws GuzzleException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/figma/{documentKey}/structure', name: "figmaDebugger", methods: ['GET'], condition: '%kernel.debug% === 1')]
    public function getFigmaDocumentStructure(string $documentKey): Response
    {
        $figmaConfig = new FigmaConfig(
            $_SERVER['FIGMA_ENDPOINT'],
            'figd_12BqTV4vIdnMOqvS3ERef9HCjOyZ4uiwznaac6ju',
            'false',
        );
        FigmaConfig::checkConfig($figmaConfig);
        $getFile = new GetFile($figmaConfig);
        $response = $getFile->getNodeIds($documentKey);

        return new JsonResponse($response);
    }

    /**
     * @throws \Exception
     * @throws GuzzleException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/figma/{documentKey}/images', name: "figmaDebuggerImages", methods: ['GET'], condition: '%kernel.debug% === 1')]
    public function getFigmaDocumentImages(string $documentKey): Response
    {
        $figmaConfig = new FigmaConfig(
            $_SERVER['FIGMA_ENDPOINT'],
            'figd_12BqTV4vIdnMOqvS3ERef9HCjOyZ4uiwznaac6ju',
            'false',
        );
        FigmaConfig::checkConfig($figmaConfig);
        $getImages = new GetImages($figmaConfig);
        $response = $getImages->forDocument($documentKey);

        return new JsonResponse($response);
    }
}
