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

namespace App\Locodio\Infrastructure\Web\Controller;

use App\Locodio\Application\AuditBus;
use App\Locodio\Domain\Model\User\User;
use DH\Auditor\Provider\Doctrine\DoctrineProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @throws \Exception
     */
    #[Route('/api/audit/domain-model/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAuditTrailForDomainModel(int $id): Response
    {
        $response = $this->auditBus->getAuditForDomainModel($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/audit/module/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAuditTrailForModule(int $id): Response
    {
        $response = $this->auditBus->getAuditTrailForModule($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/audit/enum/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAuditTrailForEnum(int $id): Response
    {
        $response = $this->auditBus->getAuditTrailForEnum($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/audit/query/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAuditTrailForQuery(int $id): Response
    {
        $response = $this->auditBus->getAuditTrailForQuery($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/audit/command/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAuditTrailForCommand(int $id): Response
    {
        $response = $this->auditBus->getAuditTrailForCommand($id);
        return new JsonResponse($response->getCollection(), 200, $this->apiAccess);
    }

}
