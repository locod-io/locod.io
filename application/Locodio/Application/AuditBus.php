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

namespace App\Locodio\Application;

use App\Locodio\Application\Query\Audit\AuditTrailCollection;
use App\Locodio\Application\Query\Audit\GetAuditTrail;
use App\Locodio\Application\Security\BasePermissionService;
use App\Locodio\Application\Security\ModelPermissionService;
use App\Locodio\Domain\Model\User\UserRepository;
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
        $this->permission = new ModelPermissionService(
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
    public function getAuditForDomainModel(int $id): AuditTrailCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckDomainModelId($id);

        $getAuditTrail = new GetAuditTrail(
            $this->provider,
            $this->userRepository,
            $this->entityManager
        );

        return $getAuditTrail->getAuditTrailForDomainModel($id);
    }

    /**
     * @throws \Exception
     */
    public function getAuditTrailForModule(int $id): AuditTrailCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckModuleId($id);

        $getAuditTrail = new GetAuditTrail(
            $this->provider,
            $this->userRepository,
            $this->entityManager
        );

        return $getAuditTrail->getAuditTrailForModule($id);
    }

    /**
     * @throws \Exception
     */
    public function getAuditTrailForEnum(int $id): AuditTrailCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckEnumId($id);

        $getAuditTrail = new GetAuditTrail(
            $this->provider,
            $this->userRepository,
            $this->entityManager
        );

        return $getAuditTrail->getAuditTrailForEnum($id);
    }

    /**
     * @throws \Exception
     */
    public function getAuditTrailForCommand(int $id): AuditTrailCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckCommandId($id);

        $getAuditTrail = new GetAuditTrail(
            $this->provider,
            $this->userRepository,
            $this->entityManager
        );

        return $getAuditTrail->getAuditTrailForCommand($id);
    }

    /**
     * @throws \Exception
     */
    public function getAuditTrailForQuery(int $id): AuditTrailCollection
    {
        $this->permission->CheckRole(['ROLE_ORGANISATION_USER']);
        $this->permission->CheckQueryId($id);

        $getAuditTrail = new GetAuditTrail(
            $this->provider,
            $this->userRepository,
            $this->entityManager
        );

        return $getAuditTrail->getAuditTrailForQuery($id);
    }

}
