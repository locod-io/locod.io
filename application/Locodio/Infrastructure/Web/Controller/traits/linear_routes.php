<?php

namespace App\Locodio\Infrastructure\Web\Controller\traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait linear_routes
{
    #[Route('/api/linear/debug', name: 'get_linear_debug', methods: ['GET'])]
    public function getLinearDebug(): Response
    {
        //        $response = $this->queryBus->getLinearIssuesByTeam('c700ba88-a84b-40b6-bcfe-6f7be19b5ca6');
        //        $response = $this->queryBus->getLinearIssueById('8edf8a1b-23a5-46ea-b6d0-53f63168dcf9');

        $response = $this->queryBus->getLinearIssueById('LOCO-20');

        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/linear/teams', name: 'get_linear_teams', methods: ['GET'])]
    public function getLinearTeams(): Response
    {
        $response = $this->queryBus->getLinearTeams();

        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
