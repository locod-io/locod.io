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

trait user_routes
{
    #[Route('/api/organisation/{organisationId}/users', requirements: ['organisationId' => '\d+'], methods: ['GET'])]
    public function getOrganisationUsers(int $organisationId): Response
    {
        $response = $this->queryBus->getUsersByOrganisation($organisationId);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{organisationId}/invitations', requirements: ['organisationId' => '\d+'], methods: ['GET'])]
    public function getOrganisationUserInvitations(int $organisationId): Response
    {
        $response = $this->queryBus->getActiveInvitationsByOrganisation($organisationId);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{organisationId}/user/{userId}', requirements: ['organisationId' => '\d+', 'userId' => '\d+'], methods: ['GET'])]
    public function getOrganisationUserDetail(int $organisationId, int $userId): Response
    {
        $response = $this->queryBus->getUserDetail(userId: $userId, organisationId: $organisationId);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{organisationId}/user/{userId}', requirements: ['organisationId' => '\d+', 'userId' => '\d+'], methods: ['POST', 'PUT'])]
    public function updateUserDetail(int $organisationId, int $userId): Response
    {
        $response = true;
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{organisationId}/invite-user', requirements: ['organisationId' => '\d+'], methods: ['POST', 'PUT'])]
    public function inviteUserToOrganisation(Request $request, int $organisationId): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->inviteUserToOrganisation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{organisationId}/remove-user/{userId}', requirements: ['organisationId' => '\d+', 'userId' => '\d+'], methods: ['POST', 'PUT'])]
    public function removeUserFromOrganisation(Request $request, int $organisationId, int $userId): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->removeUserFromOrganisation($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

    #[Route('/api/organisation/{organisationId}/user/{userId}/roles', requirements: ['organisationId' => '\d+', 'userId' => '\d+'], methods: ['POST', 'PUT'])]
    public function changeRolesForOrganisationUser(Request $request, int $organisationId, int $userId): Response
    {
        $jsonCommand = json_decode($request->request->get('command'));
        $response = $this->commandBus->changeOrganisationUserRoles($jsonCommand);
        return new JsonResponse($response, 200, $this->apiAccess);
    }

}
