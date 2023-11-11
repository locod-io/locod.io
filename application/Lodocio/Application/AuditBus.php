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

namespace App\Lodocio\Application;

use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Domain\Model\User\UserRepository;
use App\Lodocio\Application\Query\Audit\GetAuditTrail;
use App\Lodocio\Application\Query\Audit\Readmodel\AuditTrailCollection;
use App\Lodocio\Application\Security\LodocioPermissionService;
use DH\Auditor\Provider\Doctrine\DoctrineProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AuditBus
{
    protected BasePermissionService $permission;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected Security               $security,
        protected EntityManagerInterface $entityManager,
        protected bool                   $isolationMode,
        protected DoctrineProvider       $provider,
        protected UserRepository         $userRepository,
    ) {
        $this->permission = new LodocioPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Audit trail retrieval functions
    // ——————————————————————————————————————————————————————————————————————————

    /**
     * @throws \Exception
     */
    public function getNodeActivityById(int $id): AuditTrailCollection
    {
        $this->permission->CheckRole(['ROLE_USER']);
        $this->permission->CheckTrackerNodeId($id);

        $getAuditTrail = new GetAuditTrail(
            $this->provider,
            $this->userRepository,
            $this->entityManager
        );

        return $getAuditTrail->getNodeActivityById($id);
    }

}
