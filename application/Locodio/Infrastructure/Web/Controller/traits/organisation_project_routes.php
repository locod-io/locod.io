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

use App\Locodio\Application\Query\Model\GetTemplate;
use App\Locodio\Domain\Model\Model\Command;
use App\Locodio\Domain\Model\Model\DomainModel;
use App\Locodio\Domain\Model\Model\Enum;
use App\Locodio\Domain\Model\Model\Query;
use App\Locodio\Domain\Model\Model\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

trait organisation_project_routes
{
    #[Route('/api/model/organisation/{id}/teams', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getOrganisationTeams(int $id): Response
    {
        $response = $this->queryBus->getTeamsByOrganisation($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getProjectById(int $id): Response
    {
        $response = $this->queryBus->getProjectById($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}/issues', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getProjectIssuesById(int $id): Response
    {
        $response = $this->queryBus->getIssuesListByProject($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }


    #[Route('/api/model/project/{id}/download', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function downloadProjectById(int $id): Response
    {
        $response = $this->queryBus->getProjectById($id);

        //-- complete the templates with full information
        $query = new GetTemplate(
            $this->entityManager->getRepository(Template::class),
            $this->entityManager->getRepository(DomainModel::class),
            $this->entityManager->getRepository(Enum::class),
            $this->entityManager->getRepository(Query::class),
            $this->entityManager->getRepository(Command::class),
        );
        $fullTemplates = [];
        foreach ($response->getTemplates()->getCollection() as $template) {
            $fullTemplates[] = $query->ById($template->getId());
        }
        $tempResult = json_decode(json_encode($response));
        $tempResult->templates = $fullTemplates;

        // -- convert to json and temp save in the cache
        $tempExportFile = $this->appKernel->getCacheDir() . '/' . $response->getUuid() . '.json';
        file_put_contents($tempExportFile, json_encode($tempResult));

        return $this->file($tempExportFile, $response->getName() . '.json', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    #[Route('/api/model/project/{id}/documentation', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getProjectDocumentationById(int $id): Response
    {
        $response = $this->queryBus->getProjectDocumentation($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}/documentation/download', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function downloadProjectDocumentationById(int $id): Response
    {
        $response = $this->queryBus->downloadProjectDocumentation($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/model/project/{id}/create-sample-project', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function createSampleProject(int $id): Response
    {
        $response = $this->commandBus->createExampleProject($id);
        return new JsonResponse($response, 200, $this->apiAccess);
    }
}
