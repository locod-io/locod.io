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

use App\Locodio\Domain\Model\User\User;
use App\Lodocio\Application\AuditBus;
use DH\Auditor\Provider\Doctrine\DoctrineProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class AuditController extends AbstractController
{
    protected array $apiAccess;
    protected AuditBus $auditBus;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected KernelInterface        $appKernel,
        protected DoctrineProvider       $provider,
        protected Security               $security,
    ) {
        $this->apiAccess = [];
        $isolationMode = false;
        if ($this->appKernel->getEnvironment() == 'dev') {
            $this->apiAccess = array('Access-Control-Allow-Origin' => '*');
            $isolationMode = true;
        }
        $this->auditBus = new AuditBus(
            $this->security,
            $this->entityManager,
            $isolationMode,
            $this->provider,
            $entityManager->getRepository(User::class),
        );
    }

    #[Route('/api/doc/tracker/node-status/{id}/activity', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getNodeActivity(int $id, Request $request): JsonResponse
    {
        $response = $this->auditBus->getNodeActivityById($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

}
