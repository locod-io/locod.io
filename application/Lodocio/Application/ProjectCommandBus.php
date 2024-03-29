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

use App\Locodio\Infrastructure\Database\OrganisationRepository;
use App\Locodio\Infrastructure\Database\ProjectRepository;
use App\Lodocio\Application\Command\Project\AddDocProject\AddDocProjectTrait;
use App\Lodocio\Application\Command\Project\ChangeDocProject\ChangeDocProjectTrait;
use App\Lodocio\Application\Security\LodocioPermissionService;
use App\Lodocio\Domain\Model\Project\DocProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProjectCommandBus
{
    use AddDocProjectTrait;
    use ChangeDocProjectTrait;

    protected LodocioPermissionService $permission;

    public function __construct(
        protected Security               $security,
        protected EntityManagerInterface $entityManager,
        protected bool                   $isolationMode,
        protected OrganisationRepository $organisationRepository,
        protected ProjectRepository      $projectRepository,
        protected DocProjectRepository   $docProjectRepository,
    ) {
        $this->permission = new LodocioPermissionService(
            $security->getUser(),
            $entityManager,
            $this->isolationMode
        );
    }
}
